<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Registration;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;

class ScanTiket extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Scan Tiket Masuk';
    protected static ?string $title = 'Scanner Tiket Event';
    protected static string $view = 'filament.pages.scan-tiket';
    protected static ?int $navigationSort = 10;
    protected static ?string $slug = 'scan-tiket';

    // Property untuk Form Pilihan Event
    public ?array $data = [];
    public $eventId = null;

    // Property hasil scan
    public $scannedResult = null;

    public function mount(): void
    {
        // Inisialisasi form
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Konfigurasi Scanner')
                    ->description('Pilih event yang sedang berlangsung sebelum melakukan scanning.')
                    ->schema([
                        Select::make('eventId')
                            ->label('Pilih Event')
                            ->options(Event::where('status', 'published')->pluck('title', 'id'))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->placeholder('Cari nama event...')
                            ->live() // Agar properti $eventId langsung terupdate saat dipilih
                            ->afterStateUpdated(fn ($state) => $this->eventId = $state),
                    ])
            ])
            ->statePath('data');
    }

    /**
     * Fungsi Utama: Process Logic Scan
     */
    public function processTicket($code)
    {
        $this->scannedResult = null;

        // 0. Validasi: Satpam harus pilih event dulu!
        if (!$this->eventId) {
            $this->dispatch('play-sound', status: 'error');
            Notification::make()
                ->title('Pilih Event Dulu!')
                ->body('Silakan pilih event pada menu dropdown di atas sebelum scan.')
                ->danger()
                ->send();
            return;
        }

        // 1. Cari Data Registrasi
        $registration = Registration::with(['user', 'event'])
            ->where('ticket_code', $code)
            ->first();

        // --- VALIDASI START ---

        // A. Tiket Tidak Ditemukan
        if (!$registration) {
            $this->dispatch('play-sound', status: 'error');
            Notification::make()->title('Tiket Tidak Valid')->danger()->send();
            return;
        }

        // B. SALAH EVENT (Logic Baru)
        // Kalau event ID di tiket beda sama event ID yang dipilih di dropdown
        if ($registration->event_id != $this->eventId) {
            $this->dispatch('play-sound', status: 'error');
            $this->scannedResult = [
                'status' => 'error',
                'title'  => 'SALAH EVENT!',
                'desc'   => "Tiket ini untuk event: <b>{$registration->event->title}</b>. Bukan untuk event yang sedang dipilih.",
            ];
            Notification::make()->title('Salah Event')->danger()->send();
            return;
        }

        // C. Belum Lunas
        if ($registration->status === 'pending' || ($registration->payment_status !== 'paid' && $registration->payment_status !== 'free')) {
            $this->dispatch('play-sound', status: 'error');
            $this->scannedResult = [
                'status' => 'error',
                'title'  => 'BELUM LUNAS',
                'desc'   => "Peserta a.n <b>{$registration->user->name}</b> belum menyelesaikan pembayaran.",
            ];
            Notification::make()->title('Belum Lunas')->warning()->send();
            return;
        }

        // D. Tiket Batal
        if ($registration->status === 'canceled') {
            $this->dispatch('play-sound', status: 'error');
            $this->scannedResult = [
                'status' => 'error',
                'title'  => 'TIKET HANGUS',
                'desc'   => "Tiket ini sudah dibatalkan oleh sistem.",
            ];
            Notification::make()->title('Tiket Batal')->danger()->send();
            return;
        }

        // E. Sudah Masuk (Double Check-in)
        if ($registration->status === 'attended') {
            $this->dispatch('play-sound', status: 'warning'); // Suara Warning/Error
            $this->scannedResult = [
                'status' => 'warning',
                'title'  => 'SUDAH CHECK-IN',
                'desc'   => "Tiket ini sudah digunakan masuk sebelumnya pada: " . $registration->updated_at->format('H:i d/m/Y'),
                'data'   => $registration
            ];
            Notification::make()->title('Sudah Masuk')->body('Tiket sudah digunakan.')->danger()->send();
            return;
        }

        // --- VALIDASI PASS (Sukses) ---

        // 2. Update Status
        $registration->update(['status' => 'attended']);

        // 3. Feedback Sukses
        $this->dispatch('play-sound', status: 'success');
        
        $this->scannedResult = [
            'status' => 'success',
            'title'  => 'CHECK-IN BERHASIL',
            'desc'   => "Selamat Datang, <b>{$registration->user->name}</b>!",
            'data'   => $registration
        ];

        Notification::make()
            ->title('Berhasil')
            ->body("Check-in sukses: {$registration->user->name}")
            ->success()
            ->send();
    }
}