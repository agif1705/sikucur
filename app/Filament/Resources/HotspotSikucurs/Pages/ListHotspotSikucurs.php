<?php

namespace App\Filament\Resources\HotspotSikucurs\Pages;

use App\Filament\Resources\HotspotSikucurs\HotspotSikucurResource;
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
