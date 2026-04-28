<?php

namespace App\Filament\Resources\AbsensiWebPegawais\Pages;

use App\Filament\Resources\AbsensiWebPegawais\AbsensiWebPegawaiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAbsensiWebPegawai extends CreateRecord
{
    protected static string $resource = AbsensiWebPegawaiResource::class;

    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
