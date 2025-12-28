<?php

namespace App\Filament\Mahasiswa\Widgets;

use App\Models\Registration;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Ambil ID User yang sedang login
        $userId = Auth::id();

        return [
            // Statistik 1: Total Event yang diikuti
            Stat::make('Event Diikuti', Registration::where('user_id', $userId)->count())
                ->description('Total pendaftaran kamu')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]), 

            // Statistik 2: Tiket yang sudah Dikonfirmasi (Diterima)
            Stat::make('Tiket Aktif', Registration::where('user_id', $userId)->where('status', 'confirmed')->count())
                ->description('Siap digunakan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            // Statistik 3: Event Tersedia (Info umum)
            // PERBAIKAN: Menggunakan 'event_date' sesuai database kamu
            Stat::make('Event Terbaru', Event::where('event_date', '>=', now())->count())
                ->description('Event yang bisa kamu ikuti')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),
        ];
    }
}