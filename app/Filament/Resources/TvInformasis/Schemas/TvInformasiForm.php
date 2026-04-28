<?php

namespace App\Filament\Resources\TvInformasis\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TvInformasiForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Informasi')
                    ->description('Data Tv Informasi ini untuk di layar TV yang akan di tampilkan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Judul Video Youtube')
                            ->required()->columnSpan(2),
                        Forms\Components\TextInput::make('bupati')
                            ->label('Nama bupati Nagari')
                            ->required()->columnSpan(1),
                        Forms\Components\FileUpload::make('bupati_image')
                            ->label('Foto bupati Nagari')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required(),
                        Forms\Components\TextInput::make('wakil_bupati')
                            ->label('Nama wakil_bupati Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('wakil_bupati_image')
                            ->label('Foto wakil_bupati Nagari')
                            ->required(),
                        Forms\Components\TextInput::make('wali_nagari')
                            ->label('Nama wali_nagari Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('wali_nagari_image')
                            ->label('Foto wali_nagari Nagari')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required(),
                        Forms\Components\TextInput::make('bamus')
                            ->label('Nama Bamus Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('bamus_image')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->label('Foto Bamus Nagari')
                            ->required(),
                        Forms\Components\TextInput::make('babinsa')
                            ->label('Nama Babinsa Nagari')
                            ->required(),
                        Forms\Components\FileUpload::make('babinsa_image')
                            ->label('Foto Babinsa Nagari')
                            ->imageResizeMode('cover')
                            ->imageResizeTargetWidth('1920')
                            ->imageResizeTargetHeight('1080')
                            ->required(),
                        Forms\Components\RichEditor::make('running_text')
                            ->label('running_text')
                            ->required()->columns(1),
                    ])->columns(2),
            ]);
    }
}
