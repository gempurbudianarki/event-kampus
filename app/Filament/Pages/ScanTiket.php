<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Registration;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;

class ScanTiket extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Scan Tiket Masuk';
    protected static ?string $title = 'Scanner Pintu Masuk';
    protected static string $view = 'filament.pages.scan-tiket';

    // State untuk form pilih event
    public $event_id;
    
    // State untuk hasil scan
    public $scannedCode;
    public $scanResult = null; // 'success', 'error', 'warning'
    public $scanMessage = '';
    public $participantData = null;

    public function mount()
    {
        // Otomatis pilih event yang sedang aktif/paling baru (Opsional UX)
        $latestEvent = Event::latest()->first();
        if ($latestEvent) {
            $this->event_id = $latestEvent->id;
        }
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('event_id')
                ->label('Pilih Event yang Sedang Berjalan')
                ->options(Event::all()->pluck('title', 'id'))
                ->searchable()
                ->required()
                ->reactive() // Biar kalau ganti event, state ikut update
                ->helperText('Pastikan Anda memilih event yang benar sebelum scan!'),
        ];
    }

    /**
     * Method ini dipanggil via Livewire dari Javascript Scanner
     */
    public function checkTicket($code)
    {
        $this->scannedCode = $code;
        $this->scanResult = null;
        $this->participantData = null;

        // 1. Cek Apakah Event Sudah Dipilih
        if (!$this->event_id) {
            $this->scanResult = 'error';
            $this->scanMessage = 'SILAKAN PILIH EVENT TERLEBIH DAHULU!';
            
            Notification::make()->title('Pilih Event Dulu!')->danger()->send();
            return;
        }

        // 2. Cari Tiket di Database
        $registration = Registration::where('ticket_code', $code)->first();

        // 3. Validasi Tiket Tidak Ditemukan
        if (!$registration) {
            $this->scanResult = 'error';
            $this->scanMessage = 'TIKET TIDAK DITEMUKAN / TIDAK VALID!';
            
            // Play sound error (opsional di JS nanti)
            return;
        }

        // 4. Validasi Event Scope (Anti Jebol)
        if ($registration->event_id != $this->event_id) {
            $this->scanResult = 'error';
            $realEventName = $registration->event->title ?? 'Event Lain';
            $this->scanMessage = "SALAH ACARA! Tiket ini untuk: {$realEventName}";
            
            return;
        }

        // 5. Validasi Status Pembayaran
        if ($registration->status != 'confirmed') {
            $this->scanResult = 'warning';
            $this->scanMessage = "TIKET BELUM LUNAS / PENDING. Status: {$registration->status}";
            return;
        }

        // 6. Cek Apakah Sudah Check-in Sebelumnya (Opsional, biar gak masuk 2x)
        /* // Jika mau fitur sekali masuk, uncomment ini:
        if ($registration->has_checked_in) {
             $this->scanResult = 'warning';
             $this->scanMessage = 'TIKET SUDAH DIPAKAI MASUK JAM ' . $registration->check_in_time;
             return;
        }
        */

        // 7. SUKSES!
        $this->scanResult = 'success';
        $this->scanMessage = 'TIKET VALID. SILAKAN MASUK.';
        $this->participantData = [
            'name' => $registration->user->name ?? 'Guest',
            'email' => $registration->user->email ?? '-',
            'type' => $registration->payment_status == 'paid' ? 'Peserta Umum' : 'Undangan/Free',
        ];

        // Tandai Check-in di DB (Opsional)
        // $registration->update(['has_checked_in' => true, 'check_in_time' => now()]);
        
        Notification::make()->title('Check-in Berhasil')->success()->send();
    }
}