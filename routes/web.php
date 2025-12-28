<?php

use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Event; 

/*
|--------------------------------------------------------------------------
| Web Routes (Jalur Utama)
|--------------------------------------------------------------------------
*/

// 1. HALAMAN DEPAN (Landing Page)
Route::get('/', function () {
    // Kalau user iseng buka halaman depan padahal udah login
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Ambil 6 event terbaru
    $events = Event::latest()->take(6)->get();
    return view('welcome', compact('events'));
})->name('home');


// 2. REDIRECT SYSTEM & TRAFFIC POLICE
// Ini jalur "Pintar" membedakan Admin vs Mahasiswa

Route::get('/dashboard', function () {
    $user = Auth::user();

    // A. Cek Apakah Dia Admin?
    // (Logicnya sama kayak di User.php tadi)
    if ($user->email === 'admin@gmail.com') {
        return redirect('/admin');
    }

    // B. Kalau Bukan Admin, Pasti Mahasiswa
    return redirect('/mahasiswa');

})->middleware(['auth', 'verified'])->name('dashboard');


// 3. FORCE REDIRECT (Handling Link Bawaan Laravel)
// Biar user gak nyasar ke login putih polos
Route::get('/login', function () {
    return redirect('/mahasiswa/login');
})->name('login');

Route::get('/register', function () {
    return redirect('/mahasiswa/register');
})->name('register');


// 4. FITUR DOWNLOAD TIKET (Wajib Login)
Route::get('/ticket/{registration}/download', [TicketController::class, 'download'])
    ->middleware('auth')
    ->name('ticket.download');