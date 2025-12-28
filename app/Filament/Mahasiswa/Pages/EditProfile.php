<?php

namespace App\Filament\Mahasiswa\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;

class EditProfile extends BaseEditProfile
{
    // Override form bawaan Filament
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
                            ->avatar() // Tampilan bulat
                            ->imageEditor() // Fitur crop/rotate
                            ->directory('avatars') // Folder simpan: storage/app/public/avatars
                            ->rules(['image', 'max:2048']) // Max 2MB
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
                            // Saya buat disabled biar NIM gak diganti-ganti sembarangan.
                            // Kalau mau bisa diedit, hapus baris ->disabled() ini.
                            ->disabled(), 

                        TextInput::make('jurusan')
                            ->label('Jurusan')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('prodi')
                            ->label('Program Studi')
                            ->required()
                            ->maxLength(255),
                            
                        TextInput::make('no_hp')
                            ->label('Nomor HP / WhatsApp')
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
                    ->collapsed(), // Di-minimize biar gak menuhin layar
            ]);
    }
}