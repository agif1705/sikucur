<?php

namespace App\Filament\Resources\RekapAbsensiPegawaiResource\Pages;

use App\Filament\Resources\RekapAbsensiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRekapAbsensiPegawai extends EditRecord
{
    protected static string $resource = RekapAbsensiPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
