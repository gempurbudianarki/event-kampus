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
use Filament\Tables\Actions\Action;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    
    protected static ?string $navigationLabel = 'Validasi Pendaftaran';
    
    protected static ?string $navigationGroup = 'Manajemen Event';

    // Badge Angka (Total Pendaftar) di Sidebar
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pendaftaran')
                    ->description('Data mahasiswa yang mendaftar event.')
                    ->schema([
                        // 1. Pilih Event (Mengambil dari database Events kolom 'title')
                        Forms\Components\Select::make('event_id')
                            ->relationship('event', 'title') 
                            ->label('Nama Event')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // 2. Pilih Mahasiswa (Mengambil dari database Users)
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->label('Nama Mahasiswa')
                            ->searchable()
                            ->preload()
                            ->required(),

                        // 3. Kode Tiket (Otomatis Generate)
                        Forms\Components\TextInput::make('ticket_code')
                            ->label('Kode Tiket')
                            ->required()
                            ->maxLength(255)
                            ->default('TICKET-' . strtoupper(uniqid()))
                            ->readOnly(), // Admin sebaiknya tidak ubah manual biar unik

                        // 4. Status Validasi
                        Forms\Components\Select::make('status')
                            ->label('Status Validasi')
                            ->options([
                                'pending' => 'Pending (Menunggu)',
                                'confirmed' => 'Confirmed (Valid/Hadir)',
                                'rejected' => 'Rejected (Ditolak)',
                            ])
                            ->required()
                            ->default('confirmed')
                            ->native(false),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // === TOMBOL EXPORT LAPORAN DI POJOK KANAN ATAS ===
            ->headerActions([
                Action::make('export')
                    ->label('Download Laporan (.csv)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success') // Warna Hijau
                    ->url(route('export.registrations')) // Mengarah ke Controller Export
                    ->openUrlInNewTab(),
            ])
            // =================================================

            ->columns([
                // 1. Kolom Nama Mahasiswa
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->icon('heroicon-m-user'),

                // 2. Kolom Judul Event (FIX: Pakai title)
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->event->title),

                // 3. Kolom Kode Tiket
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Kode Tiket')
                    ->copyable()
                    ->copyMessage('Kode tiket disalin')
                    ->color('gray')
                    ->fontFamily('mono'),

                // 4. Kolom Status (Bisa diedit langsung)
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Status')
                    ->sortable()
                    ->selectablePlaceholder(false),

                // 5. Tanggal Daftar
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tgl Daftar')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc') // Pendaftaran terbaru paling atas
            ->filters([
                // FILTER 1: Berdasarkan Event
                SelectFilter::make('event')
                    ->relationship('event', 'title') 
                    ->label('Filter per Event')
                    ->searchable()
                    ->preload(),
                
                // FILTER 2: Berdasarkan Status
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'rejected' => 'Rejected',
                    ])
                    ->label('Filter Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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