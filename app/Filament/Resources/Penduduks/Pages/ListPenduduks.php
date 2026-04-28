<?php

namespace App\Filament\Resources\Penduduks\Pages;

use App\Filament\Resources\Penduduks\PendudukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenduduks extends ListRecords
{
    protected static string $resource = PendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
