<?php

namespace App\Filament\Resources\PermohonanSurats\Pages;

use App\Filament\Resources\PermohonanSurats\PermohonanSuratResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermohonanSurats extends ListRecords
{
    protected static string $resource = PermohonanSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
