<?php

namespace App\Filament\Resources\MikrotikConfigs\Pages;

use App\Filament\Resources\MikrotikConfigs\MikrotikConfigResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMikrotikConfigs extends ListRecords
{
    protected static string $resource = MikrotikConfigResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
