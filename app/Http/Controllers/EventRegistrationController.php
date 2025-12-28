<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification; // Kita pinjam notifikasi Filament biar keren

class EventRegistrationController extends Controller
{
    public function store(Request $request, Event $event)
    {
        // 1. Cek apakah user sudah login (Safety Double Check)
        if (!Auth::check()) {
            return redirect('/mahasiswa/login');
        }

        $user = Auth::user();

        // 2. Cek apakah dia sudah pernah daftar di event ini?
        $existingRegistration = Registration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existingRegistration) {
            // Kalau sudah daftar, balikin lagi dengan pesan error
            return redirect()->back()->with('error', 'Kamu sudah terdaftar di event ini!');
        }

        // 3. Simpan Pendaftaran Baru
        Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'status' => 'pending', // Default status: Menunggu Konfirmasi (bisa diubah jadi 'confirmed' kalau mau auto-accept)
            'registration_date' => now(),
            'notes' => 'Pendaftaran via Website',
        ]);

        // 4. Kirim Notifikasi Sukses (Menggunakan Flash Message Laravel standar biar aman di Frontend)
        return redirect()->route('dashboard')->with('success', 'Berhasil mendaftar! Cek tiketmu di menu Tiket Saya.');
    }
}