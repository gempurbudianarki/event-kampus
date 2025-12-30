<?php

namespace App\Filament\Mahasiswa\Resources\EventCatalogResource\Pages;

use App\Filament\Mahasiswa\Resources\EventCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEventCatalog extends EditRecord
{
    protected static string $resource = EventCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
