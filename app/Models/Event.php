<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', // <--- UBAH JADI TITLE (Sebelumnya name)
        'description',
        'event_date',
        'location',
        'quota',
        'price',
        'image', // Pastikan kolom ini ada di migrasi, kalau error nanti kita cek
        'status', // draft/published
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'price' => 'integer',
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}