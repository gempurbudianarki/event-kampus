<?php

namespace App\Filament\Mahasiswa\Resources\RegistrationResource\Pages;

use App\Filament\Mahasiswa\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        // KITA KOSONGKAN ARRAY INI
        // Supaya tombol "Create" / "New" HILANG.
        return [];
    }
}