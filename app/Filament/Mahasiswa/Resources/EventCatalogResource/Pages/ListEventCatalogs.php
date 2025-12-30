<?php

namespace App\Filament\Mahasiswa\Resources\EventCatalogResource\Pages;

use App\Filament\Mahasiswa\Resources\EventCatalogResource;
use Filament\Resources\Pages\ListRecords;

class ListEventCatalogs extends ListRecords
{
    protected static string $resource = EventCatalogResource::class;

    protected function getHeaderActions(): array
    {
        // KITA KOSONGKAN ARRAY INI
        return [];
    }
}