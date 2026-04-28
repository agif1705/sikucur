<?php

namespace App\Filament\Resources\WhatsAppBroadcasts\Schemas;

use App\Models\Jabatan;
use App\Models\Nagari;
use App\Models\Penduduk;
use App\Models\User;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class WhatsAppBroadcastForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Informasi Broadcast')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Broadcast')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Masukkan judul broadcast...'),

                        Forms\Components\Textarea::make('message')
                            ->label('Pesan')
                            ->required()
                            ->rows(6)
                            ->placeholder('Masukkan pesan broadcast...'."\n\n".
                                'Template yang bisa digunakan:'."\n".
                                '{name} atau {nama} - Nama pegawai/warga'."\n".
                                '{jabatan} - Jabatan pegawai'."\n".
                                '{nagari} - Nama nagari')
                            ->helperText('Gunakan template {name}, {jabatan}, {nagari} untuk personalisasi pesan')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('attachment_path')
                            ->label('Lampiran (Opsional)')
                            ->directory('broadcast-attachments')
                            ->acceptedFileTypes(['image/*', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                            ->maxSize(10240) // 10MB
                            ->helperText('Upload gambar, PDF, atau dokumen Word (maksimal 10MB)')
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                if ($state) {
                                    // Get file info
                                    $file = $state;
                                    $mimeType = $file->getMimeType();
                                    $originalName = $file->getClientOriginalName();

                                    // Set attachment type based on mime type
                                    if (str_starts_with($mimeType, 'image/')) {
                                        $set('attachment_type', 'image');
                                    } elseif ($mimeType === 'application/pdf') {
                                        $set('attachment_type', 'document');
                                    } elseif (in_array($mimeType, [
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                    ])) {
                                        $set('attachment_type', 'document');
                                    } else {
                                        $set('attachment_type', 'file');
                                    }

                                    $set('attachment_name', $originalName);
                                } else {
                                    $set('attachment_type', null);
                                    $set('attachment_name', null);
                                }
                            })
                            ->columnSpanFull(),

                        Forms\Components\Hidden::make('attachment_type'),
                        Forms\Components\Hidden::make('attachment_name'),
                    ]),

                Section::make('Target Penerima')
                    ->schema([
                        Forms\Components\Select::make('target_type')
                            ->label('Kirim ke')
                            ->options([
                                'all' => 'Semua Pegawai Aktif',
                                'nagari' => 'Berdasarkan Nagari',
                                'jabatan' => 'Berdasarkan Jabatan',
                                'penduduk' => 'Semua Warga / Penduduk',
                                'custom' => 'Pilih Manual',
                            ])
                            ->required()
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('target_ids', [])),

                        Forms\Components\Select::make('target_ids')
                            ->label('Pilih Target')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function (Get $get) {
                                return match ($get('target_type')) {
                                    'nagari' => Nagari::pluck('name', 'id')->toArray(),
                                    'jabatan' => Jabatan::pluck('name', 'id')->toArray(),
                                    'custom' => User::where('aktif', true)
                                        ->whereNotNull('no_hp')
                                        ->whereRaw("CAST(no_hp AS TEXT) != ''")
                                        ->with(['nagari', 'jabatan'])
                                        ->get()
                                        ->mapWithKeys(function ($user) {
                                            return [$user->id => $user->name.' ('.($user->jabatan->name ?? 'Tidak ada jabatan').') - '.($user->nagari->name ?? 'Tidak ada nagari')];
                                        })
                                        ->toArray(),
                                    default => []
                                };
                            })
                            ->visible(fn (Get $get) => in_array($get('target_type'), ['nagari', 'jabatan', 'custom']))
                            ->required(fn (Get $get) => in_array($get('target_type'), ['nagari', 'jabatan', 'custom']))
                            ->helperText(function (Get $get) {
                                return match ($get('target_type')) {
                                    'nagari' => 'Pilih nagari yang akan menerima broadcast',
                                    'jabatan' => 'Pilih jabatan yang akan menerima broadcast',
                                    'custom' => 'Pilih pegawai secara manual',
                                    default => ''
                                };
                            }),

                        Forms\Components\Placeholder::make('penduduk_info')
                            ->label('Info Target Penduduk')
                            ->content(function () {
                                $count = Penduduk::whereNotNull('no_hp')
                                    ->where('no_hp', '!=', '')
                                    ->where('no_hp', '!=', '0')
                                    ->count();

                                return "Akan dikirim ke semua warga/penduduk yang memiliki nomor HP (Total: {$count} orang)";
                            })
                            ->visible(fn (Get $get) => $get('target_type') === 'penduduk'),
                    ]),

                Forms\Components\Hidden::make('sender_id')
                    ->default(fn () => Auth::id()),

                Forms\Components\Hidden::make('status')
                    ->default('draft'),
            ]);
    }
}
