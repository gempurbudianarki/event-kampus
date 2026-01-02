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
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\FontFamily;
use Filament\Notifications\Notification;

class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Tiket Saya';
    protected static ?string $modelLabel = 'Tiket Saya';
    protected static ?int $navigationSort = 1;

    // Filter biar Mahasiswa cuma liat tiket dia sendiri
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', Auth::id());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Info Event (Stacked)
                Tables\Columns\TextColumn::make('event.title')
                    ->label('Event')
                    ->description(fn (Registration $record) => $record->event->event_date->format('d M Y, H:i') . ' • ' . $record->event->location)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->wrap(),

                // 2. Kode Tiket
                Tables\Columns\TextColumn::make('ticket_code')
                    ->label('Kode Tiket')
                    ->copyable()
                    ->icon('heroicon-m-qr-code')
                    ->fontFamily(FontFamily::Mono),

                // 3. Status Pembayaran
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',     // Hijau
                        'pending' => 'warning',  // Kuning
                        'failed' => 'danger',    // Merah
                        'challenge' => 'warning',
                        'free' => 'info',        // Biru (Gratis)
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => strtoupper($state)),

                // 4. Status Kehadiran
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Tiket')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'attended' => 'primary',
                        'canceled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                // --- ACTION 1: BAYAR SEKARANG (Hanya muncul jika pending) ---
                Action::make('bayar')
                    ->label('Bayar')
                    ->icon('heroicon-m-credit-card')
                    ->color('warning')
                    ->button()
                    // Modal Content ambil dari View Blade
                    ->modalContent(fn (Registration $record) => view('filament.mahasiswa.registrations.pay-modal', ['record' => $record]))
                    ->modalSubmitAction(false) // Hilangkan tombol submit default
                    ->modalCancelAction(false) // Hilangkan tombol cancel default
                    ->visible(fn (Registration $record) => $record->payment_status === 'pending' && $record->status !== 'canceled'),

                // --- ACTION 2: CEK STATUS MANUAL (JURUS ANDALAN) ---
                // Tombol ini wajib ada buat nge-bypass webhook yang macet di localhost
                Action::make('cek_status')
                    ->label('Cek Status')
                    ->icon('heroicon-m-arrow-path') // Ikon Refresh
                    ->color('info')
                    ->button()
                    ->action(function (Registration $record) {
                        try {
                            // 1. Setup Konfigurasi Midtrans (FIX: Pake Config, Jangan Env)
                            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
                            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
                            \Midtrans\Config::$isSanitized = true;
                            \Midtrans\Config::$is3ds = true;

                            // 2. Cek Status Transaksi ke Server Midtrans
                            // PENTING: Pake (object) biar gak error array vs object
                            $status = (object) \Midtrans\Transaction::status($record->ticket_code);
                            
                            $transaction = $status->transaction_status;
                            $fraud = $status->fraud_status;

                            // 3. Logic Update Database Berdasarkan Respon Midtrans
                            if ($transaction == 'capture') {
                                if ($fraud == 'challenge') {
                                    $record->update(['payment_status' => 'challenge']);
                                } else {
                                    $record->update([
                                        'payment_status' => 'paid',
                                        'status' => 'confirmed',
                                        'paid_at' => now(),
                                        'midtrans_transaction_id' => $status->transaction_id
                                    ]);
                                }
                            } else if ($transaction == 'settlement') {
                                // STATUS LUNAS
                                $record->update([
                                    'payment_status' => 'paid',
                                    'status' => 'confirmed',
                                    'paid_at' => now(),
                                    'midtrans_transaction_id' => $status->transaction_id
                                ]);
                            } else if ($transaction == 'pending') {
                                $record->update(['payment_status' => 'pending']);
                            } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                                $record->update([
                                    'payment_status' => 'failed',
                                    'status' => 'canceled'
                                ]);
                            }

                            // 4. Kirim Notifikasi Sukses
                            Notification::make()
                                ->title('Status Diperbarui')
                                ->body('Status saat ini: ' . strtoupper($transaction))
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            // Kalau Transaksi Belum Dibuat / Error Lain
                            Notification::make()
                                ->title('Gagal Cek Status')
                                ->body('Transaksi belum ditemukan di Midtrans atau koneksi error.')
                                ->danger()
                                ->send();
                        }
                    })
                    ->visible(fn (Registration $record) => $record->payment_status === 'pending'),

                // --- ACTION 3: LIHAT QR CODE (Hanya muncul jika confirmed/lunas) ---
                Action::make('qr_code')
                    ->label('Lihat QR')
                    ->icon('heroicon-m-qr-code')
                    ->color('success')
                    ->button()
                    ->modalContent(fn (Registration $record) => view('filament.mahasiswa.registrations.qr-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->visible(fn (Registration $record) => in_array($record->status, ['confirmed', 'attended'])),

                // --- ACTION 4: DOWNLOAD PDF (BARU!) ---
                Action::make('download_pdf')
                    ->label('Download PDF')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('primary') // Warna Biru biar beda sama QR
                    ->button()
                    // Mengarahkan ke Route yang kita buat tadi
                    ->url(fn (Registration $record) => route('ticket.download', $record))
                    ->openUrlInNewTab() // Buka di tab baru (UX Friendly)
                    ->visible(fn (Registration $record) => in_array($record->status, ['confirmed', 'attended'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrations::route('/'),
        ];
    }
}