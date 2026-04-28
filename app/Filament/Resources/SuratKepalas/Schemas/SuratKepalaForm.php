<?php

namespace App\Filament\Resources\SuratKepalas\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SuratKepalaForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('nagari_id')
                    ->label('Nagari')
                    ->relationship('nagari', 'name')
                    ->required(),
                Forms\Components\FileUpload::make('logo')
                    ->label('Logo Nagari')
                    ->image()
                    ->directory('copSurat')
                    ->maxSize(1024)
                    ->required()
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        // Contoh: nama file = timestamp_namaasli.ext
                        return 'Logo-kop-surat'.time().'_'.Str::uuid().'.'.$file->getClientOriginalExtension();
                    }),
                Forms\Components\RichEditor::make('kop_surat')
                    ->label('Kop Surat (Header)')
                    ->required(),
            ]);
    }
}
