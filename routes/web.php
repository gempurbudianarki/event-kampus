<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Event;
// Pastikan Controller ini nanti dibuat ya (FrontEventController)
use App\Http\Controllers\FrontEventController; 
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ReportController;


/*
|--------------------------------------------------------------------------
| Web Routes (Jalur Utama)
|--------------------------------------------------------------------------
*/

// --- 1. HALAMAN DEPAN (LANDING PAGE) ---
Route::get('/', function () {
    // Ambil event yang statusnya 'published' aja, urutkan dari yang terbaru
    // Kita ambil 9 biar pas grid-nya (3x3)
    $events = Event::where('status', 'published')
        ->latest()
        ->take(9)
        ->get();
    
    return view('welcome', compact('events'));
})->name('home');


// --- 2. TRAFFIC POLICE (Sistem Redirect Pintar) ---
Route::get('/dashboard', function () {
    $user = Auth::user();

    // A. Kalau Admin -> Lempar ke Panel Admin Filament
    if ($user->email === 'admin@ubbg.ac.id') {
        return redirect('/admin');
    }

    // B. Kalau Mahasiswa -> Lempar ke Panel Mahasiswa
    return redirect('/mahasiswa');

})->middleware(['auth', 'verified'])->name('dashboard');


// --- 3. FORCE REDIRECT (Handling Link Bawaan Laravel) ---
// Biar user gak nyasar ke halaman login default yang putih polos
Route::get('/login', function () {
    return redirect('/mahasiswa/login');
})->name('login');

Route::get('/register', function () {
    return redirect('/mahasiswa/register');
})->name('register');


// --- 4. PUBLIC EVENT ROUTES ---
// Halaman Detail Event (Nanti kita buat Controllernya)
Route::get('/event/{event}', [FrontEventController::class, 'show'])->name('event.show');

// Proses Daftar Event (Wajib Login)
Route::post('/event/{event}/register', [FrontEventController::class, 'register'])
    ->middleware('auth')
    ->name('event.register');


// --- 5. FITUR DOWNLOAD & EXPORT ---
// Download Tiket PDF (Mahasiswa)
    Route::middleware(['auth'])->group(function () {
    Route::get('/tickets/{registration}/download', [TicketController::class, 'downloadTicket'])
        ->name('ticket.download');


// Export Laporan Excel (Admin)
Route::get('/admin/export-registrations', [ReportController::class, 'export'])
    ->middleware('auth')
    ->name('export.registrations');
});