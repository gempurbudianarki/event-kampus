<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// USE FILAMENT (Wajib)
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    // PERHATIKAN: Gw udah hapus 'HasApiTokens' di sini. 
    // Jadi cuma sisa HasFactory dan Notifiable.
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * -----------------------------------------------------------------
     * LOGIKA AKSES PANEL (ADMIN vs MAHASISWA)
     * -----------------------------------------------------------------
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // 1. Panel Admin: HANYA email sakti yang boleh masuk
        if ($panel->getId() === 'admin') {
            return $this->email === 'admin@ubbg.ac.id';
        }

        // 2. Panel Mahasiswa: Semua user boleh masuk
        if ($panel->getId() === 'mahasiswa') {
            return true; 
        }

        return false;
    }
}