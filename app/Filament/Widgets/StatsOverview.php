<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\Registration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Update data tiap 15 detik biar admin liat live
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        return [
            // 1. KARTU TOTAL EVENT
            Stat::make('Event Tayang', Event::where('status', 'published')->count())
                ->description('Acara yang sedang aktif')
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            // 2. KARTU TOTAL PESERTA
            Stat::make('Total Pendaftar', Registration::count())
                ->description('Semua mahasiswa mendaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([2, 5, 8, 12, 5, 15, 20]),

            // 3. KARTU ESTIMASI OMZET (Khusus Event Berbayar)
            Stat::make('Pendapatan Tiket', function() {
                // Hitung manual: Harga Event x Jumlah Pendaftar Confirmed
                $registrations = Registration::where('status', 'confirmed')->with('event')->get();
                $total = 0;
                foreach ($registrations as $reg) {
                    if ($reg->event) {
                        $total += $reg->event->price;
                    }
                }
                return 'Rp ' . number_format($total, 0, ',', '.');
            })
                ->description('Dari tiket berbayar (Confirmed)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('warning'),
        ];
    }
}