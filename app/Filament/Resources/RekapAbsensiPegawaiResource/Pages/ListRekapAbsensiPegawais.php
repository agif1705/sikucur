<?php

namespace App\Filament\Resources\RekapAbsensiPegawaiResource\Pages;

use App\Filament\Resources\RekapAbsensiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRekapAbsensiPegawais extends ListRecords
{
    protected static string $resource = RekapAbsensiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
