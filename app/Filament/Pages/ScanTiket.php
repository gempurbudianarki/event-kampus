<?php

namespace App\Filament\Pages;

use App\Models\Event;
use App\Models\Registration;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ScanTiket extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'Scan Tiket';
    protected static ?string $title = 'Scanner Pro';
    protected static ?int $navigationSort = 3;
    
    protected static string $view = 'filament.pages.scan-tiket';

    // Variabel Data
    public $scannedData = null;
    public $eventId = null; // <--- Variabel Pilihan Event

    /**
     * Logic Utama: Cek Tiket
     */
    public function checkTicket($code)
    {
        // 0. Validasi: Admin Wajib Pilih Event Dulu!
        if (!$this->eventId) {
            $this->dispatch('play-sound', status: 'error');
            $this->notifyError('PILIH EVENT!', 'Silakan pilih event di dropdown atas sebelum scan.');
            return;
        }

        // 1. Cari Data Tiket
        $ticket = Registration::with(['user', 'event'])
            ->where('ticket_code', $code)
            ->first();

        // 2. Tiket Tidak Ditemukan
        if (!$ticket) {
            $this->dispatch('play-sound', status: 'error');
            $this->notifyError('TIKET TIDAK VALID', "Kode: $code tidak ada di database.");
            
            $this->scannedData = [
                'status' => 'Tidak Dikenali',
                'color' => 'text-red-600',
                'icon' => '🚫',
                'name' => 'Unknown',
                'email' => '-',
                'event' => '-',
            ];
            return;
        }

        // 3. CEK KESESUAIAN EVENT (Logic Baru)
        if ($ticket->event_id != $this->eventId) {
            $this->dispatch('play-sound', status: 'error');
            $this->notifyError('SALAH EVENT!', "Tiket ini untuk event: {$ticket->event->title}");
            
            $this->scannedData = [
                'status' => 'Salah Event',
                'color' => 'text-red-600',
                'icon' => '🔀', // Icon silang/tukar
                'name' => $ticket->user->name,
                'email' => $ticket->user->email,
                'event' => $ticket->event->title, // Kasih tau ini tiket event apa sebenernya
            ];
            return;
        }

        // 4. Belum Bayar
        if ($ticket->status === 'pending') {
            $this->dispatch('play-sound', status: 'error');
            $this->notifyError('BELUM LUNAS', "Peserta belum menyelesaikan pembayaran.");

            $this->scannedData = [
                'status' => 'Belum Lunas',
                'color' => 'text-yellow-600',
                'icon' => '💰',
                'name' => $ticket->user->name,
                'email' => $ticket->user->email,
                'event' => $ticket->event->title,
            ];
            return;
        }

        // 5. Sudah Masuk (Double Scan)
        if ($ticket->status === 'attended') {
            $this->dispatch('play-sound', status: 'error');
            $this->notifyError('SUDAH MASUK', "Tiket sudah discan sebelumnya.");

            $this->scannedData = [
                'status' => 'Sudah Check-in',
                'color' => 'text-yellow-600',
                'icon' => '⚠️',
                'name' => $ticket->user->name,
                'email' => $ticket->user->email,
                'event' => $ticket->event->title,
            ];
            return;
        }

        // 6. Tiket Batal
        if ($ticket->status === 'canceled') {
            $this->dispatch('play-sound', status: 'error');
            $this->notifyError('TIKET HANGUS', "Tiket statusnya batal.");
            
            $this->scannedData = [
                'status' => 'Tiket Batal',
                'color' => 'text-red-600',
                'icon' => '❌',
                'name' => $ticket->user->name,
                'email' => $ticket->user->email,
                'event' => $ticket->event->title,
            ];
            return;
        }

        // 7. SUKSES (Valid & Masuk)
        if ($ticket->status === 'confirmed') {
            $ticket->update(['status' => 'attended']);
            
            $this->dispatch('play-sound', status: 'success');
            Notification::make()->title('SILAKAN MASUK')->success()->send();

            $this->scannedData = [
                'status' => 'Check-in Berhasil',
                'color' => 'text-green-600',
                'icon' => '✅',
                'name' => $ticket->user->name,
                'email' => $ticket->user->email, // Tampilkan Email
                'event' => $ticket->event->title,
            ];
        }
    }

    protected function notifyError($title, $body)
    {
        Notification::make()->title($title)->body($body)->danger()->send();
    }
}