<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Kolom yang boleh diisi oleh Admin
    protected $fillable = [
        'title',
        'description',
        'banner',
        'event_date',
        'location',
        'quota',
        'price',
        'status',
    ];

    // Casting biar data tanggal otomatis jadi objek Carbon (gampang diolah)
    protected $casts = [
        'event_date' => 'datetime',
    ];
}