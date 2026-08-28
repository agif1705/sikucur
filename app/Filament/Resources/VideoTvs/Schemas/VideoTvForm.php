<?php

namespace App\Filament\Resources\VideoTvs\Schemas;

use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use App\Jobs\TranscodeVideo;

class VideoTvForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Detail Video')
                    ->description('Judul, urutan putar, dan status tampil di TV.')
                    ->icon('heroicon-o-film')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Video')
                            ->placeholder('mis. Profil Nagari 2026')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Putar')
                            ->helperText('Makin kecil, makin awal diputar.')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->required(),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif ditampilkan di TV')
                            ->helperText('Matikan untuk sembunyikan tanpa hapus.')
                            ->inline(false)
                            ->default(true),
                    ]),

                Section::make('Berkas Video')
                    ->description('Upload MP4. Video otomatis dikompres di latar belakang.')
                    ->icon('heroicon-o-video-camera')
                    ->schema([
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
                            ->helperText('Format disarankan MP4. Maksimal 500 MB.')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('transcode_status')
                            ->label('Status Transcode')
                            ->content(fn ($get) => self::statusBadge($get('file_path')))
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('preview')
                            ->label('Pratinjau')
                            ->visible(fn ($get) => filled($get('file_path')))
                            ->content(fn ($get) => self::preview($get('file_path')))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    /** Badge status transcode berwarna. */
    protected static function statusBadge(?string $file): HtmlString
    {
        $pill = fn (string $bg, string $fg, string $text): HtmlString => new HtmlString(
            '<span style="display:inline-flex;align-items:center;gap:.4rem;padding:.25rem .7rem;'
            . "border-radius:9999px;font-size:.8rem;font-weight:600;background:{$bg};color:{$fg};\">{$text}</span>"
        );

        if (! $file) {
            return $pill('#f3f4f6', '#4b5563', 'Belum ada file');
        }

        $statusKey = 'transcode-status/' . str_replace(['/', '\\'], '__', $file) . '.json';
        if (! Storage::disk('local')->exists($statusKey)) {
            return $pill('#f3f4f6', '#4b5563', '⏸ Menunggu antrean');
        }

        try {
            $data = json_decode(Storage::disk('local')->get($statusKey), true) ?: [];
        } catch (\Throwable) {
            return $pill('#fee2e2', '#991b1b', 'Gagal membaca status');
        }

        return match ($data['status'] ?? 'unknown') {
            'processing' => $pill('#fef3c7', '#92400e', '⏳ Sedang diproses…'),
            'done' => $pill('#dcfce7', '#166534', '✓ Selesai — ' . (isset($data['size']) ? number_format($data['size'] / 1048576, 2) . ' MB' : 'N/A')),
            'failed' => $pill('#fee2e2', '#991b1b', '✗ Gagal: ' . Str::limit($data['error'] ?? 'Tidak diketahui', 80)),
            default => $pill('#f3f4f6', '#4b5563', 'Status: ' . e($data['status'] ?? 'unknown')),
        };
    }

    /** Player pratinjau video. */
    protected static function preview(?string $file): HtmlString
    {
        if (! $file || ! Storage::disk('public')->exists($file)) {
            return new HtmlString('<span style="color:#6b7280">File belum tersedia.</span>');
        }

        $url = e(asset('storage/' . ltrim($file, '/')));

        return new HtmlString(
            '<video src="' . $url . '" controls preload="metadata" '
            . 'style="width:100%;max-width:480px;max-height:320px;border-radius:.6rem;background:#000"></video>'
        );
    }
}
