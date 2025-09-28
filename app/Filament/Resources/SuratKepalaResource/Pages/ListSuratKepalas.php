<?php

namespace App\Filament\Resources\SuratKepalaResource\Pages;

use App\Filament\Resources\SuratKepalaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSuratKepalas extends ListRecords
{
    protected static string $resource = SuratKepalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
