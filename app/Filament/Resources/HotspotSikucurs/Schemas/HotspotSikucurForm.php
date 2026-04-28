<?php

namespace App\Filament\Resources\HotspotSikucurs\Schemas;

use App\Models\HotspotSikucur;
use App\Models\MikrotikConfig;
use App\Models\Penduduk;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HotspotSikucurForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Hotspot Sikucur')
                    ->description('Edit data hotspot user yang terdaftar di Mikrotik.')
                    ->columns(2)
                    ->schema([
                        Select::make('penduduk_id')
                            ->label('Cari Penduduk')
                            ->options(function ($record) {
                                $registeredPendudukIds = HotspotSikucur::pluck('penduduk_id')->toArray();

                                if ($record) {
                                    $registeredPendudukIds = array_diff($registeredPendudukIds, [$record->penduduk_id]);
                                }

                                return Penduduk::select('id', 'name', 'nik')
                                    ->whereNotIn('id', $registeredPendudukIds)
                                    ->get()
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $penduduk = Penduduk::select('nik')->find($state);
                                    $set('nik', $penduduk?->nik);
                                    $set('nik_display', $penduduk?->nik);
                                } else {
                                    $set('nik', null);
                                    $set('nik_display', null);
                                }
                            })
                            ->afterStateHydrated(function ($component, $state, callable $set, $record) {
                                // Saat edit form dimuat, pastikan NIK terisi
                                if ($record && $record->penduduk) {
                                    $set('nik', $record->penduduk->nik);
                                    $set('nik_display', $record->penduduk->nik);
                                }
                            }),

                        Forms\Components\Hidden::make('nik')
                            ->default(fn ($record) => $record?->penduduk?->nik),
                        Forms\Components\TextInput::make('phone_mikrotik')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Select::make('mikrotik_config_id')
                            ->label('Pilih Profile')
                            ->options(MikrotikConfig::all()->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\DateTimePicker::make('expired_at')->visibleOn('edit'),
                    ]),
                Section::make('Status')
                    ->description('Aktifkan atau nonaktifkan akses hotspot user.')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Toggle::make('status')
                            ->label('Status Aktif / Nonaktif')
                            ->required(),
                        Forms\Components\TextInput::make('mikrotik_id')
                            ->required()
                            ->disabled()
                            ->maxLength(255),
                    ])->visibleOn('edit'),
            ]);
    }
}
