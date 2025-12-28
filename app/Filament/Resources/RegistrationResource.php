<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Validasi Pendaftaran';
    protected static ?string $modelLabel = 'Pendaftaran';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Admin bisa edit status manual kalau mau
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'confirmed' => 'Diterima (ACC)',
                        'rejected' => 'Ditolak',
                    ])
                    ->required(),
                
                Forms\Components\Textarea::make('notes')
                    ->label('Catatan Admin'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Nama Mahasiswa
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->user->nim), // Tampilkan NIM dibawah nama

                // Nama Event
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),

                // Tanggal Daftar
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Tgl Daftar'),

                // Status Badge
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                // Filter biar admin gampang cari yang belum di-ACC
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu Konfirmasi',
                        'confirmed' => 'Sudah Diterima',
                        'rejected' => 'Ditolak',
                    ]),
                
                SelectFilter::make('event')
                    ->relationship('event', 'title')
                    ->label('Filter per Event')
            ])
            ->actions([
                // --- TOMBOL CEPAT BUAT ACC / TOLAK ---
                Tables\Actions\Action::make('Approve')
                    ->label('Terima')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Registration $record) => $record->update(['status' => 'confirmed']))
                    ->visible(fn (Registration $record) => $record->status === 'pending'), // Cuma muncul kalau status pending

                Tables\Actions\Action::make('Reject')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Registration $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn (Registration $record) => $record->status === 'pending'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
        ];
    }
}