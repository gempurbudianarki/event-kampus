<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf; // Panggil Library PDF

class TicketController extends Controller
{
    public function download(Registration $registration)
    {
        // 1. KEAMANAN: Cek apakah tiket ini milik user yang sedang login?
        // Jangan sampai user A download tiket user B.
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Eits! Ini bukan tiket kamu.');
        }

        // 2. KEAMANAN: Cek apakah statusnya sudah confirmed?
        if ($registration->status !== 'confirmed') {
            return back()->with('error', 'Tiket belum aktif. Tunggu validasi admin.');
        }

        // 3. SIAPKAN DATA
        // Kita load view khusus PDF dan kirim data pendaftarannya
        $pdf = Pdf::loadView('tickets.pdf', [
            'registration' => $registration,
            'event' => $registration->event,
            'user' => $registration->user,
        ]);

        // 4. ATUR UKURAN KERTAS (Opsional, misal A4 atau Landscape)
        $pdf->setPaper('A4', 'landscape');

        // 5. DOWNLOAD FILE
        // Nama file: TIKET-NAMA_EVENT-KODE.pdf
        $fileName = 'TIKET-' . $registration->ticket_code . '.pdf';
        
        return $pdf->download($fileName);
    }
}