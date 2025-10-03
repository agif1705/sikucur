<?php

namespace App\Filament\Resources\MikrotikConfigResource\Pages;

use App\Filament\Resources\MikrotikConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMikrotikConfig extends EditRecord
{
    protected static string $resource = MikrotikConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
