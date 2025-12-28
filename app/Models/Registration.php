<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registration extends Model
{
    use HasFactory;

    // Menentukan kolom mana saja yang boleh diisi oleh sistem
    protected $fillable = [
        'user_id',
        'event_id',
        'status',          // status: pending, confirmed, rejected
        'payment_proof',   // Jika nanti ada upload bukti bayar
        'registration_date',
        'notes',           // Catatan tambahan jika perlu
    ];

    /**
     * Relasi ke Tabel Users
     * Satu pendaftaran pasti dimiliki oleh satu User (Mahasiswa)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Tabel Events
     * Satu pendaftaran pasti mengarah ke satu Event tertentu
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    // --- FITUR TAMBAHAN (OPSIONAL) ---
    // Ini buat helper aja, biar kalau panggil status warnanya otomatis
    // Bisa dipanggil di blade: $registration->status_color
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'confirmed' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            default => 'secondary',
        };
    }
}