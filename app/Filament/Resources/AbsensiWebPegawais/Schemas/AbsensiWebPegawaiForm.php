<?php

namespace App\Filament\Resources\AbsensiWebPegawais\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AbsensiWebPegawaiForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\Select::make('absensi')
                    ->options([
                        'HDLD' => 'Dinas Luar Daerah',
                        'HDDD' => 'Dinas Dalam Daerah',
                        'S' => 'Sakit',
                        'I' => 'Izin',
                        'C' => 'Cuti',
                    ]),
                Forms\Components\FileUpload::make('file_pendukung')
                    ->label('Foto Pendukung')
                    ->disk('public')
                    ->directory('IzinByWa')
                    ->image()
                    ->maxFiles(1)
                    ->getUploadedFileNameForStorageUsing(function ($file): string {
                        $date = now()->format('Ymd');
                        $uuid = Str::uuid();
                        $name = Auth::user()->username;
                        $ext = $file->getClientOriginalExtension(); // ambil extensi asli

                        return "izinbywa-{$name}-{$date}-{$uuid}.{$ext}";
                    })->deleteUploadedFileUsing(function ($file) {
                        Storage::disk('public')->delete($file);
                    }),
                Forms\Components\TextInput::make('alasan')
                    ->maxLength(255),
            ]);
    }
}
