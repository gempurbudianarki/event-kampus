<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ReportController extends Controller
{
    public function export()
    {
        // 1. Ambil semua data pendaftaran (urut berdasarkan Event)
        $data = Registration::with(['user', 'event'])
            ->orderBy('event_id')
            ->get();

        // 2. Buat nama file unik
        $filename = "Laporan-Pendaftar-" . date('Y-m-d-H-i-s') . ".csv";

        // 3. Buat Header File (Judul Kolom)
        $handle = fopen('php://output', 'w');
        
        // Header untuk browser biar tau ini file download
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        // 4. Proses Stream Download (Biar ringan)
        return Response::stream(function () use ($handle, $data) {
            // Tulis Judul Kolom
            fputcsv($handle, ['No', 'Nama Mahasiswa', 'Email', 'Event', 'Waktu Event', 'Kode Tiket', 'Status', 'Tanggal Daftar']);

            // Tulis Isi Data
            foreach ($data as $index => $row) {
                fputcsv($handle, [
                    $index + 1,
                    $row->user->name ?? '-',
                    $row->user->email ?? '-',
                    $row->event->title ?? '-', // Pastikan pakai title
                    $row->event->event_date->format('d M Y H:i'),
                    $row->ticket_code,
                    $row->status,
                    $row->created_at->format('d M Y H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}