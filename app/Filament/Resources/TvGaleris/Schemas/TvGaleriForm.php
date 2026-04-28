<?php

namespace App\Filament\Resources\TvGaleris\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TvGaleriForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Galeri')->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Judul Foto Dari galeri')
                        ->columns(1)
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('image')
                        ->label('Foto Dari galeri TV bisa banyak foto galery')
                        ->disk('public')
                        ->directory('galeri')
                        ->image()
                        ->dehydrateStateUsing(fn (?string $state): ?string => $state ? ltrim($state, '/') : null)
                        ->getUploadedFileNameForStorageUsing(function ($file): string {
                            $date = now()->format('Ymd');
                            $uuid = Str::uuid();
                            $ext = $file->getClientOriginalExtension(); // ambil extensi asli

                            return "galeri-{$date}-{$uuid}.{$ext}";
                        })->deleteUploadedFileUsing(function ($file) {
                            Storage::disk('public')->delete($file);
                        }),

                ]),
            ]);
    }
}
