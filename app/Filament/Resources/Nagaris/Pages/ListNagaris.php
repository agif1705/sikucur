<?php

namespace App\Filament\Resources\Nagaris\Pages;

use App\Filament\Resources\Nagaris\NagariResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNagaris extends ListRecords
{
    protected static string $resource = NagariResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
