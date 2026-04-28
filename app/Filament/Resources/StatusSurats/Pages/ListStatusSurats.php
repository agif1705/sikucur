<?php

namespace App\Filament\Resources\StatusSurats\Pages;

use App\Filament\Resources\StatusSurats\StatusSuratResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStatusSurats extends ListRecords
{
    protected static string $resource = StatusSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
