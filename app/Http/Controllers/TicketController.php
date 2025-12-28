<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function download(Registration $registration)
    {
        // 1. Validasi Keamanan: Cuma yang punya tiket yang boleh download
        if ($registration->user_id !== auth()->id()) {
            abort(403, 'Hayo mau ngintip tiket siapa?');
        }

        // 2. Load View khusus PDF (nanti kita buat di langkah 3)
        $pdf = Pdf::loadView('pdf.ticket', [
            'registration' => $registration,
            'event' => $registration->event,
            'user' => $registration->user,
        ]);

        // 3. Setup ukuran kertas (A4 atau custom kayak tiket konser)
        $pdf->setPaper('A5', 'landscape');

        // 4. Download file dengan nama unik
        return $pdf->stream('Tiket-' . $registration->event->title . '.pdf');
    }
}