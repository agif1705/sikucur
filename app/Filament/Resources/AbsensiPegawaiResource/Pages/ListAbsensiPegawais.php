<?php

namespace App\Filament\Resources\AbsensiPegawaiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AbsensiPegawaiResource;
use App\Filament\Resources\AbsensiPegawaiResource\Widgets\AbsensiHariLibur;

class ListAbsensiPegawais extends ListRecords
{
    protected static string $resource = AbsensiPegawaiResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->color('success')->label('Tambah Absensi'),
            // Actions\Action::make()->label('Sinkron FingerPrint')->color('warning'),
        ];
    }
    public function getHeading(): string
    {
        return 'Absensi Pegawai ' . now()->format('F Y');
    }
    protected function getHeaderWidgets(): array
    {
        return [
            AbsensiHariLibur::class,
        ];
    }
}
