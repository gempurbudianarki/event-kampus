<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentNotificationController extends Controller
{
    public function __construct()
    {
        // PERBAIKAN: Gunakan config() agar aman di production
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Handle notifikasi dari Midtrans (Webhook)
     */
    public function handle(Request $request)
    {
        try {
            // 1. Ambil Data Notifikasi dari Midtrans
            $notif = new Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $orderId = $notif->order_id;       // Ini adalah ticket_code kita
            $fraud = $notif->fraud_status;

            // 2. Cari Data Registrasi Berdasarkan Ticket Code
            // Gunakan lockForUpdate untuk mencegah race condition saat update status
            $registration = Registration::where('ticket_code', $orderId)->lockForUpdate()->first();

            if (!$registration) {
                // Return 404 agar Midtrans tahu data tidak ada (tapi biasanya kita return 200 biar midtrans gak retry terus)
                return response()->json(['message' => 'Registration not found'], 404);
            }

            // IDEMPOTENCY CHECK:
            // Kalau status sudah final ('confirmed' atau 'canceled'), stop proses.
            // Ini mencegah double-update jika Midtrans kirim webhook berkali-kali.
            if (in_array($registration->status, ['confirmed', 'canceled', 'attended'])) {
                return response()->json(['message' => 'Transaction already processed'], 200);
            }

            // 3. Logic Update Status
            DB::transaction(function () use ($transaction, $type, $fraud, $registration, $notif) {
                
                if ($transaction == 'capture') {
                    // Pembayaran Kartu Kredit
                    if ($type == 'credit_card') {
                        if ($fraud == 'challenge') {
                            $registration->update([
                                'payment_status' => 'challenge',
                                'status' => 'pending'
                            ]);
                        } else {
                            $registration->update([
                                'payment_status' => 'paid',
                                'status' => 'confirmed', // LUNAS & TIKET AKTIF
                                'paid_at' => now(),
                                'midtrans_transaction_id' => $notif->transaction_id,
                                'payment_method' => $type
                            ]);
                        }
                    }
                } elseif ($transaction == 'settlement') {
                    // LUNAS (Transfer, Gopay, QRIS, dll)
                    $registration->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed', // LUNAS & TIKET AKTIF
                        'paid_at' => now(),
                        'midtrans_transaction_id' => $notif->transaction_id,
                        'payment_method' => $type
                    ]);

                } elseif ($transaction == 'pending') {
                    // Menunggu Pembayaran
                    $registration->update([
                        'payment_status' => 'pending',
                        'status' => 'pending'
                    ]);

                } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                    // GAGAL / KADALUARSA
                    $registration->update([
                        'payment_status' => 'failed',
                        'status' => 'canceled' // TIKET HANGUS
                    ]);

                    // PENTING: KEMBALIKAN KUOTA EVENT!
                    // Karena user gagal bayar, kursi kosong lagi.
                    Event::where('id', $registration->event_id)->increment('quota');
                }
            });

            return response()->json(['message' => 'Notification processed'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            // Return 500 agar Midtrans mencoba kirim ulang nanti (Retry mechanism)
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}