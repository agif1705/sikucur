<?php

namespace App\Filament\Resources\UploadDokumens\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class UploadDokumenForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Upload')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('permohonan_id')
                                    ->label('Permohonan Surat')
                                    ->relationship('permohonanSurat', 'nomor_permohonan')
                                    ->required()
                                    ->searchable()
                                    ->preload(),

                                Forms\Components\Select::make('dokumen_persyaratan_id')
                                    ->label('Dokumen Persyaratan')
                                    ->relationship('dokumenPersyaratan', 'nama_dokumen')
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Dokumen')
                            ->required()
                            ->directory('dokumen-surat')
                            ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'])
                            ->maxSize(5120) // 5MB
                            ->downloadable()
                            ->previewable(),
                    ]),

                Section::make('Verifikasi Dokumen')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Status Verifikasi')
                            ->default(false)
                            ->reactive(),

                        Forms\Components\Select::make('verified_by')
                            ->label('Diverifikasi Oleh')
                            ->relationship('verifiedBy', 'name')
                            ->searchable()
                            ->preload()
                            ->visible(fn (Get $get) => $get('is_verified')),

                        Forms\Components\DateTimePicker::make('verified_at')
                            ->label('Tanggal Verifikasi')
                            ->default(now())
                            ->visible(fn (Get $get) => $get('is_verified')),

                        Forms\Components\Textarea::make('catatan_verifikasi')
                            ->label('Catatan Verifikasi')
                            ->rows(3)
                            ->visible(fn (Get $get) => $get('is_verified')),

                        Forms\Components\Textarea::make('catatan_ditolak')
                            ->label('Catatan Penolakan')
                            ->rows(3)
                            ->visible(fn (Get $get) => $get('is_verified') === false),
                    ]),
            ]);
    }
}
