<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
// Import Library Midtrans
use Midtrans\Config;
use Midtrans\Snap;

class EventRegistrationService
{
    public function __construct()
    {
        // Konfigurasi Midtrans kita pasang di sini biar terpanggil otomatis
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Menangani proses pendaftaran + Pembayaran Midtrans
     */
    public function registerUserToEvent(User $user, Event $event, string $source = 'web'): Registration
    {
        // Mulai Transaksi Database
        return DB::transaction(function () use ($user, $event, $source) {
            
            // 1. LOCKING & VALIDASI (Sama seperti sebelumnya)
            $eventLocked = Event::where('id', $event->id)->lockForUpdate()->first();

            if ($eventLocked->quota <= 0) {
                throw new Exception('Mohon maaf, kuota tiket untuk event ini sudah habis.');
            }

            $existingRegistration = Registration::where('user_id', $user->id)
                ->where('event_id', $event->id)
                ->exists();

            if ($existingRegistration) {
                throw new Exception('Anda sudah terdaftar untuk event ini sebelumnya.');
            }

            // 2. TENTUKAN STATUS & HARGA
            $isPaidEvent = $eventLocked->price > 0;
            // Kalau bayar status 'pending', kalau gratis 'confirmed'
            $status = $isPaidEvent ? 'pending' : 'confirmed';

            // 3. GENERATE TIKET CODE
            do {
                $randomStr = strtoupper(Str::random(6));
                $ticketCode = sprintf('EVT-%s-%s', now()->format('Ymd'), $randomStr);
            } while (Registration::where('ticket_code', $ticketCode)->exists());

            // 4. SIMPAN REGISTRASI (Simpan dulu biar punya ID & Code)
            $registration = Registration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_code' => $ticketCode,
                'status' => $status,
                'payment_status' => $isPaidEvent ? 'pending' : 'free', // Set status pembayaran awal
                'payment_method' => null,
            ]);

            // 5. LOGIC MIDTRANS (KHUSUS BERBAYAR)
            if ($isPaidEvent) {
                // Siapkan data untuk dikirim ke Midtrans
                $params = [
                    'transaction_details' => [
                        'order_id' => $ticketCode, // PENTING: Order ID pakai Kode Tiket
                        'gross_amount' => (int) $eventLocked->price,
                    ],
                    'customer_details' => [
                        'first_name' => $user->name,
                        'email' => $user->email,
                    ],
                    'item_details' => [
                        [
                            'id' => $event->id,
                            'price' => (int) $eventLocked->price,
                            'quantity' => 1,
                            'name' => Str::limit($event->title, 50), // Midtrans max 50 char
                        ]
                    ],
                    // Custom expiry (Misal user harus bayar dalam 2 jam)
                    'expiry' => [
                        'start_time' => now()->format('Y-m-d H:i:sO'),
                        'unit' => 'hour',
                        'duration' => 2, // Tiket hangus dalam 2 jam
                    ],
                ];

                try {
                    // Minta Snap Token ke Midtrans
                    $snapToken = Snap::getSnapToken($params);
                    
                    // Update data registrasi dengan token ini
                    $registration->update([
                        'snap_token' => $snapToken
                    ]);
                    
                } catch (Exception $e) {
                    // Kalau gagal connect Midtrans, batalkan semua (Rollback)
                    throw new Exception('Gagal memproses pembayaran: ' . $e->getMessage());
                }
            }

            // 6. KURANGI KUOTA (Booking Seat)
            // Kuota tetap kita kurangi di awal. Kalau nanti user gak bayar (expire),
            // Webhook akan mengembalikan kuota ini (Increment).
            $eventLocked->decrement('quota');

            return $registration;
        });
    }
}