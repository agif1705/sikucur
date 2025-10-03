<?php

namespace App\Filament\Resources\MikrotikConfigResource\Pages;

use App\Filament\Resources\MikrotikConfigResource;
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
