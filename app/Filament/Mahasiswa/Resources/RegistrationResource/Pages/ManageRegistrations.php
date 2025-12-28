<?php

namespace App\Filament\Mahasiswa\Resources\RegistrationResource\Pages;

use App\Filament\Mahasiswa\Resources\RegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRegistrations extends ManageRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
