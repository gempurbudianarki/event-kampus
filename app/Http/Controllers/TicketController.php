<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Download Tiket dalam bentuk PDF
     */
    public function downloadTicket(Registration $registration)
    {
        // 1. Validasi Kepemilikan (Security)
        // Cek apakah user adalah pemilik tiket ATAU admin (berdasarkan email admin)
        // Sesuai logic di User.php, admin adalah 'admin@ubbg.ac.id'
        $isAdmin = Auth::user()->email === 'admin@ubbg.ac.id';
        
        if (Auth::id() !== $registration->user_id && !$isAdmin) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Cek Status Pembayaran
        if ($registration->status !== 'confirmed') {
            return back()->with('error', 'Tiket belum lunas atau belum dikonfirmasi.');
        }

        // 3. Generate QR Code ke Base64 (Biar bisa di-embed di PDF tanpa simpan file)
        // Kita embed ticket_code yang unik
        $qrcode = base64_encode(
            QrCode::format('svg')
                  ->size(200)
                  ->errorCorrection('H')
                  ->generate($registration->ticket_code)
        );

        // 4. Load View PDF
        // Load data event dan user terkait
        $data = [
            'registration' => $registration,
            'event' => $registration->event,
            'user' => $registration->user,
            'qrcode' => $qrcode
        ];

        $pdf = Pdf::loadView('pdf.ticket', $data);
        
        // Setup ukuran kertas tiket (Opsional: A4 atau Custom)
        $pdf->setPaper('A4', 'portrait');

        // 5. Download file dengan nama cantik
        $fileName = 'TIKET-' . $registration->ticket_code . '.pdf';
        
        return $pdf->download($fileName);
    }
}