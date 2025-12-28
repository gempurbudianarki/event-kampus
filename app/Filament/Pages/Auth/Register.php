<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Pages\Auth\Register as BaseRegister;

class Register extends BaseRegister
{
    // Kita override form-nya untuk nambahin field custom
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(), // Input Nama bawaan
                        $this->getEmailFormComponent(), // Input Email bawaan
                        
                        // --- Tambahan Custom Field Kita ---
                        TextInput::make('nim')
                            ->label('NIM')
                            ->required()
                            ->numeric()
                            ->unique(table: 'users'), // Cek biar gak kembar
                        
                        TextInput::make('no_hp')
                            ->label('Nomor HP')
                            ->tel()
                            ->required(),

                        // Bisa pakai Select kalau mau pilihan jurusan tetap, atau TextInput kalau bebas
                        TextInput::make('jurusan')
                            ->label('Jurusan')
                            ->placeholder('Contoh: Ilmu Komputer')
                            ->required(),

                        TextInput::make('prodi')
                            ->label('Program Studi')
                            ->placeholder('Contoh: Informatika')
                            ->required(),
                        // ----------------------------------

                        $this->getPasswordFormComponent(), // Password bawaan
                        $this->getPasswordConfirmationFormComponent(), // Konfirmasi Password
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}