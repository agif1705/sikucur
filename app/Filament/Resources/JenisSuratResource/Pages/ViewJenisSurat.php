<?php
// filepath: app/Filament/Resources/JenisSuratResource/Pages/ViewJenisSurat.php

namespace App\Filament\Resources\JenisSuratResource\Pages;

use App\Filament\Resources\JenisSuratResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewJenisSurat extends ViewRecord
{
    protected static string $resource = JenisSuratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
