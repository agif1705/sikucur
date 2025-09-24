<?php

namespace App\Filament\Resources\AbsensiWebPegawaiResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AbsensiWebPegawaiResource;

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
            return 'Absensi WhatsApp ' . $nagariName;
        } catch (\Exception $e) {
            return 'Absensi Pegawai';
        }
    }
}