<?php

namespace App\Filament\Resources\StatusSurats\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StatusSuratForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Status')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nama_status')
                                    ->label('Nama Status')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Permohonan Masuk'),

                                Forms\Components\TextInput::make('kode_status')
                                    ->label('Kode Status')
                                    ->required()
                                    ->maxLength(5)
                                    ->unique(ignoreRecord: true)
                                    ->placeholder('Contoh: MASUK')
                                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? str($state)->upper()->toString() : null)
                                    ->extraInputAttributes(['style' => 'text-transform: uppercase']),
                            ]),

                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('warna_status')
                                    ->label('Warna Status')
                                    ->required()
                                    ->options([
                                        'primary' => 'Primary (Biru)',
                                        'secondary' => 'Secondary (Abu-abu)',
                                        'success' => 'Success (Hijau)',
                                        'danger' => 'Danger (Merah)',
                                        'warning' => 'Warning (Kuning)',
                                        'info' => 'Info (Biru Muda)',
                                        'light' => 'Light (Putih)',
                                        'dark' => 'Dark (Hitam)',
                                    ])
                                    ->default('primary'),

                                Forms\Components\TextInput::make('urutan')
                                    ->label('Urutan')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->helperText('Urutan proses status'),
                            ]),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi detail tentang status ini'),
                    ]),
            ]);
    }
}
