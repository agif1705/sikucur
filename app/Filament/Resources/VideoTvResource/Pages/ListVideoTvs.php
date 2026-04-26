<?php

namespace App\Filament\Resources\VideoTvResource\Pages;

use App\Filament\Resources\VideoTvResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVideoTvs extends ListRecords
{
    protected static string $resource = VideoTvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
