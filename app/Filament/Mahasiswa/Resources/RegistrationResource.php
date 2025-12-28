<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RegistrationResource extends Resource
{
    // Koneksi ke Model Registration
    protected static ?string $model = Registration::class;

    // Pengaturan Tampilan Menu di Sidebar
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Tiket Saya';
    protected static ?string $modelLabel = 'Tiket Event';
    protected static ?string $pluralModelLabel = 'Tiket Saya';
    protected static ?int $navigationSort = 1;

    // --- PENTING: FILTER DATA USER ---
    // Fungsi ini memastikan mahasiswa cuma bisa lihat tiket punya dia sendiri.
    // Data orang lain otomatis disembunyikan.
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Field Nama Event (Ambil dari relasi event->title)
                Forms\Components\TextInput::make('event.title')
                    ->label('Nama Event')
                    ->formatStateUsing(fn ($record) => $record->event->title ?? '-')
                    ->disabled(), // Kita kunci biar gak bisa diedit user

                // Field Status
                Forms\Components\TextInput::make('status')
                    ->label('Status Pendaftaran')
                    ->disabled(),
                
                // Field Tanggal Daftar
                Forms\Components\DateTimePicker::make('created_at')
                    ->label('Tanggal Daftar')
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Kolom Gambar Banner (Sesuai database lo: 'banner')
                Tables\Columns\ImageColumn::make('event.banner')
                    ->label('Poster')
                    ->circular(),

                // 2. Kolom Nama Event (Sesuai database lo: 'title')
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Nama Acara')
                    ->description(fn (Registration $record): string => 
                        'Lokasi: ' . ($record->event->location ?? '-')
                    )
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                // 3. Kolom Tanggal Event (Sesuai database lo: 'event_date')
                Tables\Columns\TextColumn::make('event.event_date')
                    ->label('Jadwal')
                    ->dateTime('d M Y, H:i') // Format tanggal Indonesia banget
                    ->sortable(),

                // 4. Kolom Status (Badge Warna-warni)
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success', // Hijau
                        'pending' => 'warning',   // Kuning
                        'rejected' => 'danger',   // Merah
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
            ])
            ->defaultSort('created_at', 'desc') // Urutkan dari yang paling baru daftar
            ->actions([
                // Tombol 1: Lihat Detail (Modal Pop-up)
                Tables\Actions\ViewAction::make()
                    ->label('Detail')
                    ->modalWidth('md'),

                // Tombol 2: CETAK TIKET (Fitur Premium)
                Tables\Actions\Action::make('download_ticket')
                    ->label('Cetak Tiket')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    // Logic: Tombol ini cuma muncul kalau statusnya sudah 'confirmed'
                    ->visible(fn (Registration $record) => $record->status === 'confirmed')
                    // Logic: Klik tombol -> Buka route download PDF
                    ->url(fn (Registration $record) => route('ticket.download', $record))
                    ->openUrlInNewTab(), // Buka di tab baru biar dashboard gak ke-close
            ])
            ->bulkActions([]); // Matikan fitur hapus massal biar aman
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRegistrations::route('/'),
        ];
    }
}