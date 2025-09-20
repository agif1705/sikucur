<?php

namespace App\Filament\Resources\UploadDokumenResource\Pages;

use App\Filament\Resources\UploadDokumenResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUploadDokumen extends EditRecord
{
    protected static string $resource = UploadDokumenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
