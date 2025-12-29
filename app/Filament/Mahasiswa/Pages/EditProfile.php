<?php

namespace App\Filament\Mahasiswa\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Auth;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Section 1: Foto Profil
                Section::make('Foto Profil')
                    ->description('Upload foto terbaikmu (Format: JPG/PNG, Max: 2MB)')
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Avatar')
                            ->avatar()
                            ->imageEditor()
                            ->directory('avatars')
                            ->rules(['image', 'max:2048'])
                            ->columnSpanFull(),
                    ]),

                // Section 2: Data Diri
                Section::make('Data Akademik')
                    ->description('Pastikan data sesuai dengan Kartu Tanda Mahasiswa (KTM).')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        
                        TextInput::make('email')
                            ->label('Email Kampus')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        TextInput::make('nim')
                            ->label('NIM')
                            ->numeric()
                            ->required()
                            // --- SMART LOCK LOGIC ---
                            // Hanya disable kalau user sudah punya NIM di database.
                            // Jadi pas pertama kali isi, ini terbuka.
                            ->disabled(fn () => Auth::user()->nim !== null)
                            ->hint(fn () => Auth::user()->nim !== null ? 'Hubungi admin jika ingin mengubah NIM.' : 'Pastikan NIM benar, tidak bisa diubah nanti.'),

                        TextInput::make('jurusan')
                            ->label('Jurusan')
                            ->placeholder('Contoh: Teknik Informatika')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('prodi')
                            ->label('Program Studi')
                            ->placeholder('Contoh: S1 Sistem Informasi')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('no_hp')
                            ->label('WhatsApp Aktif')
                            ->tel()
                            ->required()
                            ->maxLength(20),
                    ])->columns(2),

                // Section 3: Ganti Password
                Section::make('Keamanan Akun')
                    ->description('Kosongkan jika tidak ingin mengganti password.')
                    ->schema([
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->collapsed(),
            ]);
    }
}