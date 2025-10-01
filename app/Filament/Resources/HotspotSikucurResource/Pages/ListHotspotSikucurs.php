<?php

namespace App\Filament\Resources\HotspotSikucurResource\Pages;

use App\Filament\Resources\HotspotSikucurResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHotspotSikucurs extends ListRecords
{
    protected static string $resource = HotspotSikucurResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
