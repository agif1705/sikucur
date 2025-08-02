<?php

namespace App\Filament\Resources\TvGaleriResource\Pages;

use App\Filament\Resources\TvGaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTvGaleris extends ListRecords
{
    protected static string $resource = TvGaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
