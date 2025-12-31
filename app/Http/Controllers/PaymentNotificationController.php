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
        // Set konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
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
            // Kita lock baris ini biar gak bentrok kalau ada update barengan
            $registration = Registration::where('ticket_code', $orderId)->lockForUpdate()->first();

            if (!$registration) {
                return response()->json(['message' => 'Registration not found'], 404);
            }

            // Kalau status sudah 'confirmed' atau 'canceled', jangan diapa-apain lagi (Idempotency)
            if (in_array($registration->status, ['confirmed', 'canceled', 'attended'])) {
                return response()->json(['message' => 'Transaction already processed'], 200);
            }

            // 3. Logic Update Status Berdasarkan Respon Midtrans
            DB::transaction(function () use ($transaction, $type, $fraud, $registration, $notif) {
                
                // Status Transaksi Midtrans:
                // capture, settlement = LUNAS
                // pending = MENUNGGU
                // deny, expire, cancel = GAGAL

                if ($transaction == 'capture') {
                    // Untuk pembayaran kartu kredit
                    if ($type == 'credit_card') {
                        if ($fraud == 'challenge') {
                            $registration->update([
                                'payment_status' => 'challenge',
                                'status' => 'pending' // Masih ragu-ragu
                            ]);
                        } else {
                            $registration->update([
                                'payment_status' => 'paid',
                                'status' => 'confirmed', // LUNAS!
                                'paid_at' => now(),
                                'midtrans_transaction_id' => $notif->transaction_id,
                                'payment_method' => $type
                            ]);
                        }
                    }
                } elseif ($transaction == 'settlement') {
                    // LUNAS (Transfer, Gopay, dll masuk sini)
                    $registration->update([
                        'payment_status' => 'paid',
                        'status' => 'confirmed', // Tiket jadi AKTIF
                        'paid_at' => now(),
                        'midtrans_transaction_id' => $notif->transaction_id,
                        'payment_method' => $type
                    ]);

                } elseif ($transaction == 'pending') {
                    // Menunggu pembayaran
                    $registration->update([
                        'payment_status' => 'pending',
                        'status' => 'pending'
                    ]);

                } elseif ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                    // PEMBAYARAN GAGAL / KADALUARSA
                    $registration->update([
                        'payment_status' => 'failed',
                        'status' => 'canceled' // Tiket hangus
                    ]);

                    // PENTING: KEMBALIKAN KUOTA EVENT!
                    // Karena waktu daftar kuota udah kita potong, kalau gagal harus dibalikin.
                    Event::where('id', $registration->event_id)->increment('quota');
                }
            });

            return response()->json(['message' => 'Notification processed'], 200);

        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}