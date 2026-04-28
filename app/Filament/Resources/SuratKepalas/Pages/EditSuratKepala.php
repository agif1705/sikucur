<?php

namespace App\Filament\Resources\SuratKepalas\Pages;

use App\Filament\Resources\SuratKepalas\SuratKepalaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSuratKepala extends EditRecord
{
    protected static string $resource = SuratKepalaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
