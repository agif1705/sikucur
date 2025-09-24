<?php

namespace App\Filament\Resources\RekapAbsensiPegawaiResource\Pages;

use App\Filament\Resources\RekapAbsensiPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRekapAbsensiPegawai extends CreateRecord
{
    protected static string $resource = RekapAbsensiPegawaiResource::class;
    protected function getRedirectUrl(): string
    {
        // redirect ke list setelah create
        return $this->getResource()::getUrl('index');
    }
}
