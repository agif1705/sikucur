<?php

namespace App\Filament\Resources\ListYoutubeResource\Pages;

use App\Filament\Resources\ListYoutubeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListListYoutubes extends ListRecords
{
    protected static string $resource = ListYoutubeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
