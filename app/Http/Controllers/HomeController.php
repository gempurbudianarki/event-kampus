<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil event yang statusnya 'published' DAN tanggalnya belum lewat
        // Kita urutkan dari yang paling dekat tanggal mainnya
        $events = Event::where('status', 'published')
                        ->where('event_date', '>=', now())
                        ->orderBy('event_date', 'asc')
                        ->get();

        return view('welcome', compact('events'));
    }
}