<?php

namespace App\Filament\Resources\DokumenPersyaratans\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DokumenPersyaratanForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Dokumen')
                    ->schema([

                        Forms\Components\Select::make('jenis_surat_id')
                            ->label('Jenis Surat')
                            ->relationship('jenisSurat', 'nama_jenis')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([

                                Forms\Components\TextInput::make('nama_jenis')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('kode_surat')
                                    ->required()
                                    ->maxLength(10),
                            ]),

                        Grid::make(2)
                            ->schema([

                                Forms\Components\TextInput::make('nama_dokumen')
                                    ->label('Nama Dokumen')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: Fotocopy KTP'),

                                Forms\Components\TextInput::make('urutan')
                                    ->label('Urutan')
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->helperText('Urutan tampil dokumen'),
                            ]),

                        Forms\Components\Toggle::make('is_wajib')
                            ->label('Dokumen Wajib')
                            ->default(true)
                            ->helperText('Centang jika dokumen ini wajib dilengkapi'),

                        Forms\Components\Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->rows(3)
                            ->placeholder('Keterangan tambahan tentang dokumen ini'),
                    ]),
            ]);
    }
}
