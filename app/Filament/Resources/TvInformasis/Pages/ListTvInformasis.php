<?php

namespace App\Filament\Resources\TvInformasis\Pages;

use App\Filament\Resources\TvInformasis\TvInformasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTvInformasis extends ListRecords
{
    protected static string $resource = TvInformasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
