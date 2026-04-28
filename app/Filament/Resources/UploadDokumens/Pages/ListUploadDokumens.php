<?php

namespace App\Filament\Resources\UploadDokumens\Pages;

use App\Filament\Resources\UploadDokumens\UploadDokumenResource;
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
