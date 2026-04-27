<?php

namespace App\Filament\Resources\AbsensiWebPegawaiResource\Pages;

use App\Filament\Resources\AbsensiWebPegawaiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListAbsensiWebPegawais extends ListRecords
{
    protected static string $resource = AbsensiWebPegawaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getHeading(): string
    {
        try {
            $nagariName = Auth::user()->nagari->name ?? 'Unknown';

            return 'Absensi WhatsApp '.$nagariName;
        } catch (\Exception $e) {
            return 'Absensi Pegawai';
        }
    }
}
