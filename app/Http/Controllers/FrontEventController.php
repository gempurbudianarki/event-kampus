<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FrontEventController extends Controller
{
    // 1. TAMPILKAN DETAIL EVENT
    public function show(Event $event)
    {
        // Cek apakah user sudah login & sudah daftar event ini?
        $isRegistered = false;
        if (Auth::check()) {
            $isRegistered = Registration::where('user_id', Auth::id())
                ->where('event_id', $event->id)
                ->exists();
        }

        return view('event.show', compact('event', 'isRegistered'));
    }

    // 2. PROSES PENDAFTARAN (KLIK TOMBOL DAFTAR)
    public function register(Event $event)
    {
        $user = Auth::user();

        // VALIDASI 1: Cek Kuota
        if ($event->quota <= 0) {
            return back()->with('error', 'Mohon maaf, kuota tiket sudah habis! 😭');
        }

        // VALIDASI 2: Cek Apakah Sudah Daftar?
        $existing = Registration::where('user_id', $user->id)
            ->where('event_id', $event->id)
            ->first();

        if ($existing) {
            return back()->with('warning', 'Kamu sudah terdaftar di event ini sebelumnya.');
        }

        // PROSES SIMPAN (Pakai DB Transaction biar aman)
        DB::transaction(function () use ($event, $user) {
            // A. Kurangi Kuota Event
            $event->decrement('quota');

            // B. Simpan Data Pendaftaran
            Registration::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'ticket_code' => 'TICKET-' . strtoupper(uniqid()), // Generate tiket unik
                'status' => 'confirmed', // Langsung aktif (kecuali mau sistem bayar manual)
            ]);
        });

        // Sukses -> Lempar ke Dashboard Mahasiswa
        return redirect()->route('dashboard')->with('success', 'Selamat! Pendaftaran berhasil. Tiket sudah terbit. 🎉');
    }
}