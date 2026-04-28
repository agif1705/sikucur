<?php

namespace App\Filament\Resources\VideoTvs\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoTvForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Video TV')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Video')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Video')
                            ->disk('public')
                            ->directory('video-tv')
                            ->acceptedFileTypes([
                                'video/mp4',
                                'video/webm',
                                'video/ogg',
                                'video/quicktime',
                                'video/x-msvideo',
                                'video/x-matroska',
                            ])
                            ->maxSize(512000)
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->getUploadedFileNameForStorageUsing(function ($file): string {
                                $date = now()->format('Ymd');
                                $uuid = Str::uuid();
                                $ext = $file->getClientOriginalExtension();

                                return "video-tv-{$date}-{$uuid}.{$ext}";
                            })
                            ->deleteUploadedFileUsing(function ($file): void {
                                Storage::disk('public')->delete($file);
                            })
                            ->helperText('Format disarankan MP4. Maksimal 500 MB.'),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Putar')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif ditampilkan di TV')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }
}
