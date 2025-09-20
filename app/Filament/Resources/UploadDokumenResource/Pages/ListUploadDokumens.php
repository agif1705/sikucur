<?php

namespace App\Filament\Resources\UploadDokumenResource\Pages;

use App\Filament\Resources\UploadDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUploadDokumens extends ListRecords
{
    protected static string $resource = UploadDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
