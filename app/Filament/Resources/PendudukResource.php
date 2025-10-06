<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Penduduk;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\MikrotikConfig;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Imports\ProductImporter;
use Filament\Tables\Actions\ImportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Imports\PendudukImporter;
use App\Filament\Resources\PendudukResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PendudukResource\RelationManagers;

class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;

    protected static ?string $navigationIcon = 'gmdi-people-tt';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Data Penduduk')
                    ->description('Pastikan data yang dimasukkan sesuai dengan KTP')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Masukan Nama Lengkap Sesuai KTP')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nik')
                            ->label('Masukan NIK Lengkap Sesuai KTP')
                            ->required()
                            ->numeric() // Hapus parameter (16) - ini tidak valid
                            ->minLength(16) // Minimal 16 karakter
                            ->maxLength(16) // Maksimal 16 karakter
                            ->unique(ignoreRecord: true)
                            ->rules([
                                'regex:/^[0-9]{16}$/', // Regex untuk memastikan tepat 16 digit angka
                            ])
                            ->validationAttribute('NIK')
                            ->helperText('NIK harus terdiri dari 16 digit angka')
                            ->placeholder('Contoh: 1234567890123456'),
                        Forms\Components\TextInput::make('alamat')
                            ->label('Masukan Alamat Lengkap Sesuai KTP')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('jk')
                            ->label('Masukan Jenis Kelamin Sesuai KTP')
                            ->options([
                                '1' => 'Laki-laki',
                                '2' => 'Perempuan',
                            ])
                            ->required(),
                        Forms\Components\TextInput::make('tempat_lahir')
                            ->label('Masukan Tempat Lahir Lengkap Sesuai KTP')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('tanggal_lahir')
                            ->label('Masukan Tanggal Lahir Sesuai KTP')
                            ->required(),
                        Forms\Components\TextInput::make('korong')
                            ->label('Masukan Korong Sesuai KTP ')
                            ->required(),
                        Forms\Components\TextInput::make('kk')
                            ->label('Masukan Nomor KK Sesuai KTP')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('kepala_keluarga')
                            ->label('Masukan Kepala Keluarga Sesuai KTP')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_hp')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make('Hostpot Sikucur')
                    ->description('Pendaftaran hotspot hanya untuk penduduk yang berdomisili di wilayah Sikucur')
                    ->schema([
                        Forms\Components\Select::make('mikrotik_config_id')
                            ->label('Cari dan pilih wilayah hotspot')
                            ->options(MikrotikConfig::where('nagari', 'sikucur')
                                ->where('is_active', true)
                                ->pluck('name', 'id'))
                            ->required()
                            ->afterStateHydrated(function ($component, $record, $state) {
                                if ($record) {
                                    // Jika ada data hotspot, gunakan config yang sudah tersimpan
                                    if ($record->hotspotSikucur) {
                                        $component->state($record->hotspotSikucur->mikrotik_config_id);
                                    }
                                    // Jika tidak ada data hotspot dan state kosong, set default
                                    elseif (empty($state)) {
                                        $nagari = Auth::user()->nagari->slug ?? 'sikucur';
                                        $defaultConfig = MikrotikConfig::where('nagari', $nagari)
                                            ->where('is_active', true)
                                            ->first();
                                        if ($defaultConfig) {
                                            $component->state($defaultConfig->id);
                                        }
                                    }
                                }
                            }),
                    ])->columns(3),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                IconColumn::make('jk')->label('L/P')
                    ->icon(fn(string $state): string => match ($state) {
                        '1' => 'gmdi-male',
                        '2' => 'gmdi-female-o',
                    })->color(fn(string $state): string => match ($state) {
                        '1' => 'success',
                        '2' => 'warning',
                    }),
                TextColumn::make('nik')->searchable(),
                TextColumn::make('hotspotSikucur.mikrotikConfig.name')
                    ->label('Wilayah Hotspot')
                    ->searchable()
                    ->placeholder('Belum terdaftar')
                    ->badge()
                    ->color('success'),
                TextColumn::make('tanggal_lahir')->searchable(),
                TextColumn::make('tanggal_lahir')->searchable(),
            ])->defaultSort('updated_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenduduks::route('/'),
            'create' => Pages\CreatePenduduk::route('/create'),
            'edit' => Pages\EditPenduduk::route('/{record}/edit'),
        ];
    }
}
