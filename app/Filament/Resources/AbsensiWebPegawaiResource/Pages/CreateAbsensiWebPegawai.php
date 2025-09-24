<?php

namespace App\Filament\Resources\AbsensiWebPegawaiResource\Pages;

use App\Filament\Resources\AbsensiWebPegawaiResource;
use Filament\Actions;
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
