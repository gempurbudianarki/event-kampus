<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\EventCatalogResource\Pages;
use App\Models\Event;
use App\Models\Registration;
use App\Services\EventRegistrationService; // IMPORT SERVICE SATPAM
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Layout\View;
use Illuminate\Support\HtmlString;
use Exception;

class EventCatalogResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Jelajah Event';
    protected static ?string $modelLabel = 'Jelajah Event';
    protected static ?string $slug = 'jelajah-event';
    protected static ?int $navigationSort = 2;

    public static function getEloquentQuery(): Builder
    {
        // Hanya tampilkan event yang statusnya Published
        return parent::getEloquentQuery()->where('status', 'published');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // Grid Responsive
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                // Tampilan Kartu Custom
                View::make('filament.mahasiswa.events.card')
            ])
            ->actions([
                // --- TOMBOL 1: LIHAT DETAIL ---
                Action::make('view_detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->modalHeading(fn (Event $record) => $record->title)
                    ->modalContent(fn (Event $record) => view('filament.mahasiswa.events.detail-modal', ['record' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup')
                    ->extraAttributes(['class' => 'w-full mb-2']),

                // --- TOMBOL 2: DAFTAR (NOW SECURED BY SERVICE) ---
                Action::make('daftar')
                    // Logic Tampilan Tombol (UI Only - Read Operation)
                    ->label(function (Event $record) {
                        if (Registration::where('user_id', Auth::id())->where('event_id', $record->id)->exists()) {
                            return 'Sudah Terdaftar';
                        }
                        if ($record->quota <= 0) return 'Kuota Habis';
                        return 'Daftar Sekarang';
                    })
                    ->color(function (Event $record) {
                        if (Registration::where('user_id', Auth::id())->where('event_id', $record->id)->exists()) {
                            return 'success';
                        }
                        if ($record->quota <= 0) return 'danger';
                        return 'primary';
                    })
                    ->icon(function (Event $record) {
                        if (Registration::where('user_id', Auth::id())->where('event_id', $record->id)->exists()) {
                            return 'heroicon-m-check-badge';
                        }
                        return 'heroicon-m-ticket';
                    })
                    ->button()
                    ->extraAttributes(['class' => 'w-full shadow-lg'])

                    // Form Syarat & Ketentuan
                    ->form([
                        Forms\Components\Placeholder::make('terms_text')
                            ->label('Syarat & Ketentuan Event')
                            ->content(new HtmlString('
                                <div class="text-sm text-gray-600 dark:text-gray-300 space-y-2 border p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                                    <p class="font-bold text-gray-800 dark:text-white">Harap baca dengan teliti sebelum mendaftar:</p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Data diri yang digunakan untuk mendaftar harus <b>valid</b>.</li>
                                        <li>Tiket <b>tidak dapat dipindahtangankan</b>.</li>
                                        <li>Jika event berbayar, biaya <b>tidak dapat dikembalikan</b> (No Refund).</li>
                                    </ul>
                                </div>
                            ')),
                        Forms\Components\Checkbox::make('terms_accepted')
                            ->label('Saya menyetujui persyaratan di atas.')
                            ->required(),
                    ])
                    
                    ->modalHeading('Konfirmasi Pendaftaran')
                    ->modalSubmitActionLabel('Setuju & Daftar')

                    // EKSEKUSI PENDAFTARAN (WRITE OPERATION)
                    // Disini kita panggil Service, bukan nulis logic manual lagi.
                    ->action(function (Event $record, array $data) {
                        try {
                            // Panggil Satpam Pusat
                            $service = app(EventRegistrationService::class);
                            $registration = $service->registerUserToEvent(Auth::user(), $record, 'filament-mahasiswa');

                            // Cek Status untuk pesan notifikasi
                            if ($registration->status === 'confirmed') {
                                Notification::make()
                                    ->title('Pendaftaran Berhasil!')
                                    ->body("Tiket Anda: {$registration->ticket_code}. Silakan cek menu Tiket Saya.")
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Pendaftaran Berhasil')
                                    ->body('Silakan segera lakukan pembayaran untuk mengaktifkan tiket.')
                                    ->warning()
                                    ->send();
                            }

                        } catch (Exception $e) {
                            // Tangkap Error dari Service (Misal: Kuota Habis / Sudah Daftar)
                            Notification::make()
                                ->title('Gagal Mendaftar')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                            
                            // Hentikan proses
                            return;
                        }
                    })
                    // Disable tombol jika kondisi tidak memungkinkan (UI Safeguard)
                    ->disabled(fn (Event $record) => 
                        $record->quota <= 0 || 
                        Registration::where('user_id', Auth::id())->where('event_id', $record->id)->exists()
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEventCatalogs::route('/'),
        ];
    }
}