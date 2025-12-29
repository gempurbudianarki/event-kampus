<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class RegistrationChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Pendaftaran Mahasiswa';
    
    // Urutan ke-2 (di bawah kartu statistik, di atas tabel)
    protected static ?int $sort = 2; 
    
    // Tinggi grafik biar pas
    protected static ?string $maxHeight = '300px';

    // Refresh otomatis tiap 15 detik biar admin liat live
    protected static ?string $pollingInterval = '15s';

    protected function getData(): array
    {
        // LOGIKA: Ambil data 7 hari terakhir
        $data = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Label Tanggal (contoh: 30 Dec)
            $labels[] = $date->format('d M'); 
            
            // Hitung jumlah pendaftar pada tanggal tersebut
            $data[] = Registration::whereDate('created_at', $date->format('Y-m-d'))->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Mahasiswa Mendaftar',
                    'data' => $data,
                    'borderColor' => '#6366f1', // Warna Indigo (Keren)
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)', // Warna isi transparan
                    'fill' => true,
                    'tension' => 0.4, // Garis melengkung halus (smooth)
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // Tipe Grafik Garis
    }
}