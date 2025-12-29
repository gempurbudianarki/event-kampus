<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Wajib buat Transaction
use Illuminate\Support\Str;        // Wajib buat Random String

class EventRegistrationController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // 1. Cek Login (Security Layer 1)
        if (!Auth::check()) {
            return redirect('/mahasiswa/login');
        }

        $user = Auth::user();

        // 2. Mulai Transaksi Database (Security Layer 2 - Anti Race Condition)
        // Kita pakai try-catch biar kalau ada error, database rollback otomatis
        try {
            return DB::transaction(function () use ($event, $user) {
                
                // A. Kunci Data Event (Locking)
                // lockForUpdate() bikin user lain harus nunggu sampai proses ini kelar.
                // Penting banget pas rebutan tiket (war ticket).
                $eventLocked = Event::where('id', $event->id)->lockForUpdate()->first();

                // B. Cek Kuota di dalam Lock
                if ($eventLocked->quota <= 0) {
                    // Kita lempar error biar ditangkap catch di bawah
                    throw new \Exception('Yah, kuota tiket sudah habis!'); 
                }

                // C. Cek apakah user sudah pernah daftar?
                $existingRegistration = Registration::where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->exists();

                if ($existingRegistration) {
                    throw new \Exception('Kamu sudah terdaftar di event ini!');
                }

                // D. Generate Kode Tiket Unik
                // Format: EVT-YYYYMMDD-RANDOM (Contoh: EVT-20241230-AK72)
                do {
                    $randomCode = strtoupper(Str::random(6));
                    $ticketCode = 'EVT-' . now()->format('Ymd') . '-' . $randomCode;
                } while (Registration::where('ticket_code', $ticketCode)->exists()); // Cek biar gak duplikat

                // E. Simpan Data Pendaftaran
                Registration::create([
                    'user_id' => $user->id,
                    'event_id' => $event->id,
                    'ticket_code' => $ticketCode, // INI YANG SEBELUMNYA KURANG
                    'status' => 'confirmed', // Untuk MVP kita auto-confirm dulu (kecuali berbayar nanti beda logic)
                    'registration_date' => now(),
                    'notes' => 'Pendaftaran via Website',
                ]);

                // F. Kurangi Kuota Event
                $eventLocked->decrement('quota');

                // Sukses! Redirect ke Dashboard
                return redirect()->route('dashboard')
                    ->with('success', 'Berhasil! Tiket kamu: ' . $ticketCode);
            });

        } catch (\Exception $e) {
            // Kalau ada error (Kuota habis / Sudah daftar), masuk ke sini
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}