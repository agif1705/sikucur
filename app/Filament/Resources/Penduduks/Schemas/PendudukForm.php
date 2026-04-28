<?php

namespace App\Filament\Resources\Penduduks\Schemas;

use App\Models\MikrotikConfig;
use App\Models\WaliKorong;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class PendudukForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                static::dataPendudukSection(),
                static::hotspotSection(),
            ]);
    }

    private static function dataPendudukSection(): Section
    {
        return Section::make('Data Penduduk')
            ->description('Pastikan data yang dimasukkan sesuai dengan KTP')
            ->columns([
                'default' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Lengkap Sesuai KTP')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('nik')
                    ->label('NIK Lengkap Sesuai KTP')
                    ->required()
                    ->numeric()
                    ->minLength(16)
                    ->maxLength(16)
                    ->unique(ignoreRecord: true)
                    ->rules(['regex:/^[0-9]{16}$/'])
                    ->validationAttribute('NIK')
                    ->helperText('NIK harus terdiri dari 16 digit angka')
                    ->placeholder('Contoh: 1234567890123456'),

                Forms\Components\TextInput::make('alamat')
                    ->label('Alamat Lengkap Sesuai KTP')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Textarea::make('alamat_domisili')
                    ->label('Alamat Lengkap Domisili')
                    ->rows(3)
                    ->columnSpanFull()
                    ->helperText('Isi jika alamat domisili berbeda dari alamat KTP. Jika kosong, surat memakai alamat KTP.'),

                Forms\Components\Select::make('jk')
                    ->label('Jenis Kelamin Sesuai KTP')
                    ->options([
                        '1' => 'Laki-laki',
                        '2' => 'Perempuan',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('tempat_lahir')
                    ->label('Tempat Lahir Sesuai KTP')
                    ->required()
                    ->maxLength(255),

                Forms\Components\DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir Sesuai KTP')
                    ->required(),

                Forms\Components\Select::make('korong')
                    ->label('Korong Sesuai KTP')
                    ->options(fn () => static::korongOptions())
                    ->searchable()
                    ->preload()
                    ->required()
                    ->helperText('Pilihan korong diambil dari data Wali Korong.'),

                Forms\Components\TextInput::make('kk')
                    ->label('Nomor KK Sesuai KTP')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('kepala_keluarga')
                    ->label('Kepala Keluarga Sesuai KTP')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('no_hp')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    private static function hotspotSection(): Section
    {
        return Section::make('Hotspot Sikucur')
            ->description('Pendaftaran hotspot hanya untuk penduduk yang berdomisili di wilayah Sikucur')
            ->columns([
                'default' => 1,
                'md' => 2,
                'xl' => 3,
            ])
            ->schema([
                Forms\Components\Select::make('mikrotik_config_id')
                    ->label('Wilayah Hotspot')
                    ->options(fn () => static::mikrotikConfigOptions())
                    ->required()
                    ->afterStateHydrated(function ($component, $record, $state) {
                        if (! $record) {
                            return;
                        }

                        if ($record->hotspotSikucur) {
                            $component->state($record->hotspotSikucur->mikrotik_config_id);

                            return;
                        }

                        if (blank($state) && ($defaultConfig = static::defaultMikrotikConfig())) {
                            $component->state($defaultConfig->id);
                        }
                    }),
            ]);
    }

    private static function korongOptions(): array
    {
        return WaliKorong::query()
            ->orderBy('wilayah')
            ->pluck('wilayah', 'wilayah')
            ->all();
    }

    private static function mikrotikConfigOptions(): array
    {
        return MikrotikConfig::query()
            ->where('nagari', 'sikucur')
            ->where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    private static function defaultMikrotikConfig(): ?MikrotikConfig
    {
        $nagari = Auth::user()->nagari->slug ?? 'sikucur';

        return MikrotikConfig::query()
            ->where('nagari', $nagari)
            ->where('is_active', true)
            ->first();
    }
}
