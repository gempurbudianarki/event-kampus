<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontEventController extends Controller
{
    // 1. Menampilkan Halaman Detail Event
    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    // 2. Proses Pendaftaran Event
    public function register(Event $event)
    {
        // Cek apakah user sudah login?
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login dulu untuk mendaftar.');
        }

        $user = Auth::user();

        // Cek 1: Apakah event masih buka / kuota ada?
        if ($event->quota <= 0) {
            return back()->with('error', 'Mohon maaf, kuota event ini sudah habis!');
        }

        // Cek 2: Apakah user SUDAH pernah daftar di event ini?
        $existingRegistration = Registration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existingRegistration) {
            return back()->with('warning', 'Anda sudah terdaftar di event ini sebelumnya.');
        }

        // PROSES SIMPAN DATA (Pakai DB Transaction biar aman)
        DB::transaction(function () use ($event, $user) {
            // A. Simpan ke tabel registrations
            $registration = Registration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'status' => 'confirmed', // Atau 'pending' kalau ada pembayaran
                'ticket_code' => 'TICKET-' . strtoupper(uniqid()), // Generate kode unik
            ]);

            // B. Kurangi Kuota Event
            $event->decrement('quota');
        });

        // Redirect ke dashboard atau halaman sukses
        return redirect()->route('dashboard')->with('success', 'Berhasil mendaftar event! Tiket sudah diterbitkan.');
    }
}