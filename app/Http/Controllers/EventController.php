<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    // 1. Tampilkan Halaman Form Create
    public function create()
    {
        return view('admin.events.create');
    }

    // 2. Simpan Data Event ke Database
    public function store(Request $request)
    {
        // Validasi Input (Biar gak ngasal)
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date|after:today', // Tanggal harus masa depan
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'banner' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // Upload Gambar Banner
        $bannerPath = null;
        if ($request->hasFile('banner')) {
            // Simpan di folder: storage/app/public/banners
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }

        // Simpan ke Database
        Event::create([
            'title' => $request->title,
            'description' => $request->description,
            'event_date' => $request->event_date,
            'location' => $request->location,
            'quota' => $request->quota,
            'banner' => $bannerPath,
            'status' => 'published', // Default langsung tayang
        ]);

        // Redirect balik ke Dashboard dengan pesan sukses
        return redirect()->route('dashboard')->with('success', 'Event berhasil dibuat, Bro!');
    }
}