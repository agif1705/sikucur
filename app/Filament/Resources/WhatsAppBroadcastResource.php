<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Nagari;
use App\Models\Jabatan;
use App\Models\Penduduk;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\WhatsAppBroadcast;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Services\WhatsAppBroadcastService;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\WhatsAppBroadcastResource\Pages;
use App\Filament\Resources\WhatsAppBroadcastResource\RelationManagers;

class WhatsAppBroadcastResource extends Resource
{
    protected static ?string $model = WhatsAppBroadcast::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'WhatsApp Broadcast';

    protected static ?string $modelLabel = 'WhatsApp Broadcast';

    protected static ?string $pluralModelLabel = 'WhatsApp Broadcasts';

    protected static ?string $navigationGroup = 'Broadcast & Notifikasi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Broadcast')
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
                            ->placeholder('Masukkan pesan broadcast...' . "\n\n" .
                                'Template yang bisa digunakan:' . "\n" .
                                '{name} atau {nama} - Nama pegawai/warga' . "\n" .
                                '{jabatan} - Jabatan pegawai' . "\n" .
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
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
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

                Forms\Components\Section::make('Target Penerima')
                    ->schema([
                        Forms\Components\Select::make('target_type')
                            ->label('Kirim ke')
                            ->options([
                                'all' => 'Semua Pegawai Aktif',
                                'nagari' => 'Berdasarkan Nagari',
                                'jabatan' => 'Berdasarkan Jabatan',
                                'penduduk' => 'Semua Warga / Penduduk',
                                'custom' => 'Pilih Manual'
                            ])
                            ->required()
                            ->default('all')
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('target_ids', [])),

                        Forms\Components\Select::make('target_ids')
                            ->label('Pilih Target')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function (Forms\Get $get) {
                                return match ($get('target_type')) {
                                    'nagari' => Nagari::pluck('name', 'id')->toArray(),
                                    'jabatan' => Jabatan::pluck('name', 'id')->toArray(),
                                    'custom' => User::where('aktif', true)
                                        ->whereNotNull('no_hp')
                                        ->whereRaw("CAST(no_hp AS TEXT) != ''")
                                        ->with(['nagari', 'jabatan'])
                                        ->get()
                                        ->mapWithKeys(function ($user) {
                                            return [$user->id => $user->name . ' (' . ($user->jabatan->name ?? 'Tidak ada jabatan') . ') - ' . ($user->nagari->name ?? 'Tidak ada nagari')];
                                        })
                                        ->toArray(),
                                    default => []
                                };
                            })
                            ->visible(fn(Forms\Get $get) => in_array($get('target_type'), ['nagari', 'jabatan', 'custom']))
                            ->required(fn(Forms\Get $get) => in_array($get('target_type'), ['nagari', 'jabatan', 'custom']))
                            ->helperText(function (Forms\Get $get) {
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
                            ->visible(fn(Forms\Get $get) => $get('target_type') === 'penduduk'),
                    ]),

                Forms\Components\Hidden::make('sender_id')
                    ->default(fn() => Auth::id()),

                Forms\Components\Hidden::make('status')
                    ->default('draft'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender.name')
                    ->label('Pengirim')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('has_attachment')
                    ->label('Lampiran')
                    ->boolean()
                    ->state(fn(WhatsAppBroadcast $record): bool => !empty($record->attachment_path))
                    ->trueIcon('heroicon-o-paper-clip')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),

                Tables\Columns\TextColumn::make('target_type')
                    ->label('Target')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'all' => 'success',
                        'nagari' => 'info',
                        'jabatan' => 'warning',
                        'penduduk' => 'primary',
                        'custom' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'all' => 'Semua',
                        'nagari' => 'Nagari',
                        'jabatan' => 'Jabatan',
                        'penduduk' => 'Penduduk',
                        'custom' => 'Manual',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('total_recipients')
                    ->label('Total Penerima')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_sent')
                    ->label('Terkirim')
                    ->numeric()
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_failed')
                    ->label('Gagal')
                    ->numeric()
                    ->sortable()
                    ->color('danger'),

                Tables\Columns\TextColumn::make('success_rate')
                    ->label('Tingkat Keberhasilan')
                    ->state(function (WhatsAppBroadcast $record): string {
                        return $record->total_recipients > 0
                            ? round(($record->total_sent / $record->total_recipients) * 100, 1) . '%'
                            : '0%';
                    })
                    ->badge()
                    ->color(function (WhatsAppBroadcast $record): string {
                        $rate = $record->total_recipients > 0
                            ? ($record->total_sent / $record->total_recipients) * 100
                            : 0;
                        return match (true) {
                            $rate >= 90 => 'success',
                            $rate >= 70 => 'warning',
                            $rate >= 50 => 'info',
                            default => 'danger',
                        };
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'sending' => 'Mengirim',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Dikirim Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'sending' => 'Mengirim',
                        'completed' => 'Selesai',
                        'failed' => 'Gagal',
                    ]),

                Tables\Filters\SelectFilter::make('target_type')
                    ->label('Target')
                    ->options([
                        'all' => 'Semua',
                        'nagari' => 'Nagari',
                        'jabatan' => 'Jabatan',
                        'penduduk' => 'Penduduk',
                        'custom' => 'Manual',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('send')
                    ->label('Kirim')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->visible(fn(WhatsAppBroadcast $record): bool => $record->status === 'draft')
                    ->requiresConfirmation()
                    ->modalHeading('Kirim Broadcast')
                    ->modalDescription('Apakah Anda yakin ingin mengirim broadcast ini? Setelah dikirim, broadcast tidak bisa dibatalkan.')
                    ->action(function (WhatsAppBroadcast $record) {
                        $broadcastService = app(WhatsAppBroadcastService::class);
                        $success = $broadcastService->sendBroadcast($record);

                        if ($success) {
                            Notification::make()
                                ->title('Broadcast berhasil dikirim!')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Broadcast gagal dikirim!')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('view_logs')
                    ->label('Lihat Log')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(fn(WhatsAppBroadcast $record): string =>
                    static::getUrl('logs', ['record' => $record])),

                Tables\Actions\EditAction::make()
                    ->visible(fn(WhatsAppBroadcast $record): bool => $record->status === 'draft'),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn(WhatsAppBroadcast $record): bool => $record->status === 'draft'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => true),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWhatsAppBroadcasts::route('/'),
            'create' => Pages\CreateWhatsAppBroadcast::route('/create'),
            'edit' => Pages\EditWhatsAppBroadcast::route('/{record}/edit'),
            'logs' => Pages\ViewBroadcastLogs::route('/{record}/logs'),
        ];
    }
}
