<?php

namespace App\Filament\Resources\TvGaleriResource\Pages;

use App\Filament\Resources\TvGaleriResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTvGaleri extends EditRecord
{
    protected static string $resource = TvGaleriResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
