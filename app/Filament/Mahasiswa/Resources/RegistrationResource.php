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
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Tiket Saya';
    protected static ?string $pluralModelLabel = 'Tiket Saya';
    protected static ?int $navigationSort = 1;

    // Filter: Cuma tampilkan tiket milik user yang login
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ticket_code')
                    ->label('Kode Tiket')
                    ->readOnly(),
                Forms\Components\TextInput::make('status')
                    ->readOnly(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Poster Event
                Tables\Columns\ImageColumn::make('event.image')
                    ->label('Poster')
                    ->circular()
                    ->defaultImageUrl(url('/images/placeholder.png')),

                // 2. Info Event
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (Registration $record) => $record->event->event_date->format('d M Y, H:i') . ' WIB')
                    ->wrap(),

                // 3. Kode Tiket
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Kode Tiket')
                    ->copyable()
                    ->fontFamily('mono')
                    ->color('primary'),

                // 4. Status
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmed' => 'Aktif',
                        'pending' => 'Menunggu Pembayaran',
                        'rejected' => 'Ditolak',
                        default => $state,
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                // --- ACTION 1: UPLOAD BUKTI BAYAR (Jika Pending) ---
                Action::make('upload_payment')
                    ->label('Upload Bukti')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->button()
                    ->visible(fn (Registration $record) => $record->status === 'pending')
                    ->form([
                        FileUpload::make('payment_proof')
                            ->label('Bukti Transfer')
                            ->image()
                            ->directory('payment-proofs')
                            ->required()
                            ->maxSize(2048), // Max 2MB
                    ])
                    ->action(function (Registration $record, array $data) {
                        // Update data bukti bayar
                        // Pastikan kolom 'payment_proof' ada di database!
                        $record->update([
                            'payment_proof' => $data['payment_proof'],
                        ]);
                        
                        Notification::make()
                            ->title('Bukti Terkirim')
                            ->body('Admin akan memverifikasi pembayaran kamu.')
                            ->success()
                            ->send();
                    }),

                // --- ACTION 2: DOWNLOAD PDF (Jika Confirmed) ---
                Action::make('download')
                    ->label('Download PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->button()
                    ->url(fn (Registration $record) => route('ticket.download', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Registration $record) => $record->status === 'confirmed'),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
        ];
    }
}