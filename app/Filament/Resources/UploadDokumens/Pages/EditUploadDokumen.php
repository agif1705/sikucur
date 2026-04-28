<?php

namespace App\Filament\Resources\UploadDokumens\Pages;

use App\Filament\Resources\UploadDokumens\UploadDokumenResource;
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
