<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable;

    /**
     * Data yang boleh diisi massal.
     * Admin cukup isi name, email, password.
     * Mahasiswa wajib isi semuanya via Form Register.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',
        'no_hp',
        'jurusan',
        'prodi',
        'avatar_url',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * LOGIC SATPAM PINTU MASUK (Panel Access)
     * Ini yang membedakan Admin dan Mahasiswa
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // 1. JALUR ADMIN (Sangat Ketat)
        if ($panel->getId() === 'admin') {
            // Cuma email ini yang boleh masuk jadi Admin
            // Ganti dengan email admin lo kalau mau beda
            return $this->email === 'admin@gmail.com'; 
        }

        // 2. JALUR MAHASISWA (Umum)
        if ($panel->getId() === 'mahasiswa') {
            // Semua user yang punya akun boleh masuk sini
            // Termasuk Admin kalau mau ngintip dashboard mahasiswa
            return true;
        }

        return false;
    }

    /**
     * Logic Foto Profil (Avatar)
     */
    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->avatar_url) {
            return Storage::url($this->avatar_url);
        }
        return null;
    }
}