<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\EventRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class EventRegistrationController extends Controller
{
    protected $registrationService;

    // Dependency Injection: Kita "suntikkan" Service Satpam ke sini.
    // Laravel otomatis akan menyiapkan service ini saat Controller dipanggil.
    public function __construct(EventRegistrationService $registrationService)
    {
        $this->registrationService = $registrationService;
    }

    public function store(Request $request, Event $event)
    {
        // 1. Cek Login (Security Layer 1)
        if (!Auth::check()) {
            return redirect('/mahasiswa/login');
        }

        $user = Auth::user();

        try {
            // 2. Delegasikan ke Service (Otak Sentral)
            // Semua logic rumit (Locking DB, Cek Kuota, Transaksi) sudah diurus Service.
            // Kita tinggal panggil fungsinya. Code jadi jauh lebih rapi & aman.
            $registration = $this->registrationService->registerUserToEvent($user, $event, 'web');

            // 3. Sukses -> Redirect ke Dashboard
            return redirect()->route('dashboard')
                ->with('success', 'Berhasil! Tiket kamu: ' . $registration->ticket_code);

        } catch (Exception $e) {
            // 4. Gagal -> Balikin User dengan Pesan Error
            // Error message diambil langsung dari Exception yang dilempar Service
            // (Contoh: "Kuota habis", "Sudah terdaftar", dll)
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}