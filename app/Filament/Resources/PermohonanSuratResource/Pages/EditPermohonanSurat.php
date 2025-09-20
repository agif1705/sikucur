<?php

namespace App\Filament\Resources\PermohonanSuratResource\Pages;

use App\Filament\Resources\PermohonanSuratResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermohonanSurat extends EditRecord
{
    protected static string $resource = PermohonanSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
