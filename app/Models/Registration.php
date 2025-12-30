<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    // DAFTARKAN SEMUA KOLOM DI SINI BIAR BISA DISIMPAN
    protected $fillable = [
        'user_id',
        'event_id',
        'ticket_code',   // <-- Ini yang bikin error tadi
        'status',
        'payment_proof', // <-- Ini buat fitur upload bukti bayar nanti
    ];

    // RELASI KE USER
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // RELASI KE EVENT
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}