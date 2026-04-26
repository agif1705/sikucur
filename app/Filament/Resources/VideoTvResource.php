<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoTvResource\Pages;
use App\Models\VideoTv;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoTvResource extends Resource
{
    protected static ?string $model = VideoTv::class;

    protected static string | \UnitEnum | null $navigationGroup = 'Tv Informasi';
    protected static ?string $navigationLabel = 'Video TV';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-video-camera';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari.name')
                    ->label('Nagari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Video')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->before(function (VideoTv $record): void {
                        if ($record->file_path) {
                            Storage::disk('public')->delete($record->file_path);
                        }
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->before(function ($records): void {
                            foreach ($records as $record) {
                                if ($record->file_path) {
                                    Storage::disk('public')->delete($record->file_path);
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideoTvs::route('/'),
            'create' => Pages\CreateVideoTv::route('/create'),
            'edit' => Pages\EditVideoTv::route('/{record}/edit'),
        ];
    }
}
