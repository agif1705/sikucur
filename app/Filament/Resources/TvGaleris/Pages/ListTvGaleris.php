<?php

namespace App\Filament\Resources\TvGaleris\Pages;

use App\Filament\Resources\TvGaleris\TvGaleriResource;
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
