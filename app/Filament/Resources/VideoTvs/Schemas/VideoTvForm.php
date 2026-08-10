<?php

namespace App\Filament\Resources\VideoTvs\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Jobs\TranscodeVideo;
use Illuminate\Support\Facades\Bus;

class VideoTvForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Video TV')
                    ->schema([
                        Forms\Components\Placeholder::make('transcode_status')
                            ->label('Status Transcode')
                            ->content(function ($get) {
                                $file = $get('file_path');
                                if (! $file) {
                                    return 'Tidak ada file terupload.';
                                }

                                $statusKey = 'transcode-status/' . str_replace(["/", "\\"], '__', $file) . '.json';
                                if (! Storage::disk('local')->exists($statusKey)) {
                                    return 'Menunggu antrean transcoding (belum diproses).';
                                }

                                try {
                                    $data = json_decode(Storage::disk('local')->get($statusKey), true);
                                } catch (\Throwable $e) {
                                    return 'Gagal membaca status.';
                                }

                                return match ($data['status'] ?? 'unknown') {
                                    'processing' => 'Sedang diproses',
                                    'done' => 'Selesai — ukuran: ' . (isset($data['size']) ? number_format($data['size'] / 1024 / 1024, 2) . ' MB' : 'N/A'),
                                    'failed' => 'Gagal: ' . ($data['error'] ?? 'Tidak diketahui'),
                                    default => 'Status: ' . ($data['status'] ?? 'unknown'),
                                };
                            })
                            ->columnSpan(2),

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
                            ->afterStateUpdated(function (?string $state) {
                                if (! $state) {
                                    return;
                                }

                                // Dispatch transcoding job. Ensure ffmpeg is installed on the server.
                                try {
                                    TranscodeVideo::dispatch($state, 'public');
                                } catch (\Throwable $e) {
                                    \Illuminate\Support\Facades\Log::error('Failed to dispatch TranscodeVideo', ['error' => $e->getMessage()]);
                                }
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
