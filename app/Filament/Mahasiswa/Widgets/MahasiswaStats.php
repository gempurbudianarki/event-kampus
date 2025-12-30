<?php

namespace App\Filament\Mahasiswa\Widgets;

use App\Models\Registration;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class MahasiswaStats extends BaseWidget
{
    // Biar gak terlalu sering refresh (beban server)
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $userId = Auth::id();

        return [
            // 1. STATISTIK TOTAL DAFTAR
            Stat::make('Total Event Diikuti', Registration::where('user_id', $userId)->count())
                ->description('Riwayat pendaftaranmu')
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('primary')
                ->chart([1, 3, 2, 5, 4, 8]), // Grafik hiasan

            // 2. STATISTIK TIKET AKTIF (CONFIRMED)
            Stat::make('Tiket Aktif', Registration::where('user_id', $userId)->where('status', 'confirmed')->count())
                ->description('Siap digunakan check-in')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3]),

            // 3. INFO EVENT TERBARU
            Stat::make('Event Tersedia', Event::where('status', 'published')->count())
                ->description('Cek event baru sekarang!')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),
        ];
    }
}