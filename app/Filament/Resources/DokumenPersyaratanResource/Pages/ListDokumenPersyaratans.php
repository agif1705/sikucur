<?php

namespace App\Filament\Resources\DokumenPersyaratanResource\Pages;

use App\Filament\Resources\DokumenPersyaratanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDokumenPersyaratans extends ListRecords
{
    protected static string $resource = DokumenPersyaratanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
