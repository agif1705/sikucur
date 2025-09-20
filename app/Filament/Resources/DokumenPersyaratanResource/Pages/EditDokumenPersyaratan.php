<?php

namespace App\Filament\Resources\DokumenPersyaratanResource\Pages;

use App\Filament\Resources\DokumenPersyaratanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDokumenPersyaratan extends EditRecord
{
    protected static string $resource = DokumenPersyaratanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
