<?php

namespace App\Filament\Widgets;

use App\Models\Registration;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestRegistrations extends BaseWidget
{
    protected static ?int $sort = 3; // Urutan paling bawah
    protected static ?string $heading = '5 Pendaftar Terakhir';
    
    // Biar widgetnya lebar full (span full width)
    protected int | string | array $columnSpan = 'full'; 

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ambil 5 data terakhir
                Registration::with(['user', 'event'])->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Mahasiswa')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->limit(30),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Daftar')
                    ->since(), // Format: "2 minutes ago"

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->paginated(false); // Matikan pagination biar simpel
    }
}