<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    // Kita buka semua kolom biar aman diisi (Asal controllernya ketat)
    protected $guarded = ['id'];

    // Casting tipe data biar enak dipanggil
    protected $casts = [
        'registration_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Event
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}