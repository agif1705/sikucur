<?php

namespace App\Filament\Resources\AbsensiWebPegawaiResource\Pages;

use App\Filament\Resources\AbsensiWebPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbsensiWebPegawai extends EditRecord
{
    protected static string $resource = AbsensiWebPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}