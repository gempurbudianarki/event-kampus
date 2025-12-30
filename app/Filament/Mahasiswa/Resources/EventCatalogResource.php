<?php

namespace App\Filament\Mahasiswa\Resources;

use App\Filament\Mahasiswa\Resources\EventCatalogResource\Pages;
use App\Models\Event;
use App\Models\Registration;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Layout\View; // Untuk Tampilan Kartu Custom
use Illuminate\Support\HtmlString; // Untuk render HTML di dalam modal

class EventCatalogResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Jelajah Event';
    protected static ?string $modelLabel = 'Jelajah Event';
    protected static ?string $slug = 'jelajah-event';
    protected static ?int $navigationSort = 2;

    // Filter hanya event yang statusnya Published
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('status', 'published');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // GRID RESPONSIVE (Laptop 3 kolom, Tablet 2 kolom)
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->columns([
                // PANGGIL FILE BLADE KARTU (filament/mahasiswa/events/card.blade.php)
                View::make('filament.mahasiswa.events.card')
            ])
            ->actions([
                // --- TOMBOL 1: LIHAT DETAIL (MODAL POPUP) ---
                Action::make('view_detail')
                    ->label('Lihat Detail')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->modalHeading(fn (Event $record) => $record->title)
                    ->modalContent(fn (Event $record) => view('filament.mahasiswa.events.detail-modal', ['record' => $record]))
                    ->modalSubmitAction(false) // Hilangkan tombol submit default
                    ->modalCancelActionLabel('Tutup')
                    ->extraAttributes(['class' => 'w-full mb-2']), // Full width button

                // --- TOMBOL 2: DAFTAR DENGAN KONFIRMASI SYARAT (LOGIC UTAMA) ---
                Action::make('daftar')
                    // 1. Label Tombol Dinamis
                    ->label(function (Event $record) {
                        if (Registration::where('user_id', Auth::id())->where('event_id', $record->id)->exists()) {
                            return 'Sudah Terdaftar';
                        }
                        if ($record->quota <= 0) return 'Kuota Habis';
                        return 'Daftar Sekarang';
                    })
                    // 2. Warna Tombol Dinamis
                    ->color(function (Event $record) {
                        if (Registration::where('user_id', Auth::id())->where('event_id', $record->id)->exists()) {
                            return 'success'; // Hijau
                        }
                        if ($record->quota <= 0) return 'danger'; // Merah
                        return 'primary'; // Biru
                    })
                    // 3. Ikon Dinamis
                    ->icon(function (Event $record) {
                        if (Registration::where('user_id', Auth::id())->where('event_id', $record->id)->exists()) {
                            return 'heroicon-m-check-badge';
                        }
                        return 'heroicon-m-ticket';
                    })
                    ->button()
                    ->extraAttributes(['class' => 'w-full shadow-lg']) // CSS Shadow

                    // 4. FORM KONFIRMASI (POPUP SYARAT)
                    ->form([
                        // Teks Persyaratan (HTML + CSS Tailwind)
                        Forms\Components\Placeholder::make('terms_text')
                            ->label('Syarat & Ketentuan Event')
                            ->content(new HtmlString('
                                <div class="text-sm text-gray-600 dark:text-gray-300 space-y-2 border p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700">
                                    <p class="font-bold text-gray-800 dark:text-white">Harap baca dengan teliti sebelum mendaftar:</p>
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Data diri yang digunakan untuk mendaftar harus <b>valid</b>.</li>
                                        <li>Peserta wajib hadir <b>15 menit</b> sebelum acara dimulai.</li>
                                        <li>Tiket <b>tidak dapat dipindahtangankan</b> tanpa izin panitia.</li>
                                        <li>Peserta wajib menjaga ketertiban selama acara berlangsung.</li>
                                        <li>Jika event berbayar, biaya pendaftaran <b>tidak dapat dikembalikan</b> (No Refund).</li>
                                    </ul>
                                </div>
                            ')),

                        // Checkbox Wajib Centang
                        Forms\Components\Checkbox::make('terms_accepted')
                            ->label('Saya telah membaca dan menyetujui semua persyaratan di atas.')
                            ->required() // WAJIB DIISI
                            ->validationAttribute('Persetujuan Syarat'),
                    ])
                    
                    ->modalHeading('Konfirmasi Pendaftaran')
                    ->modalDescription('Silakan setujui persyaratan di bawah untuk melanjutkan.')
                    ->modalSubmitActionLabel('Setuju & Daftar')

                    // 5. ACTION EKSEKUSI (Hanya jalan jika checkbox dicentang)
                    ->action(function (Event $record, array $data) {
                        $user = Auth::user();

                        // A. Cek Kuota (Server Side)
                        if ($record->quota <= 0) {
                            Notification::make()->title('Gagal')->body('Yah, kuota event ini sudah habis!')->danger()->send();
                            return;
                        }

                        // B. Cek Duplikat (Server Side)
                        if (Registration::where('user_id', $user->id)->where('event_id', $record->id)->exists()) {
                            Notification::make()->title('Info')->body('Kamu sudah terdaftar di event ini.')->warning()->send();
                            return;
                        }

                        // C. Tentukan Status
                        // Gratis -> Confirmed
                        // Bayar -> Pending
                        $status = $record->price == 0 ? 'confirmed' : 'pending';
                        
                        // Kurangi kuota jika langsung confirmed
                        if($status == 'confirmed') {
                            $record->decrement('quota');
                        }

                        // D. Simpan ke Database
                        Registration::create([
                            'user_id' => $user->id,
                            'event_id' => $record->id,
                            'ticket_code' => 'TICKET-' . strtoupper(uniqid()),
                            'status' => $status,
                        ]);

                        // E. Notifikasi Berhasil
                        if ($status == 'confirmed') {
                            Notification::make()
                                ->title('Pendaftaran Berhasil!')
                                ->body('Tiket sudah diterbitkan. Cek menu "Tiket Saya".')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Pendaftaran Berhasil')
                                ->body('Silakan upload bukti bayar di menu "Tiket Saya" agar tiket aktif.')
                                ->warning()
                                ->send();
                        }
                    })
                    // Matikan tombol jika kuota habis atau sudah daftar (UI Side)
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