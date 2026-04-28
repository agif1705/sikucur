<?php

namespace App\Filament\Resources\RekapAbsensiPegawais\Pages;

use App\Filament\Resources\RekapAbsensiPegawais\RekapAbsensiPegawaiResource;
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

    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
