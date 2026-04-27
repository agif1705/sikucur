<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TvGaleriResource\Pages;
use App\Models\TvGaleri;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TvGaleriResource extends Resource
{
    protected static ?string $model = TvGaleri::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Tv Informasi';

    protected static ?string $navigationLabel = 'Galeri TV';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tv';

    public static function form(Schema $form): Schema
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
                        ->directory('galeri')
                        ->image()
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Judul Foto Dari galeri')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image')
                    ->square(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),

            ])->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function ($record) {
                        // hapus file dari storage
                        if ($record->image) {
                            Storage::disk('public')->delete($record->image);
                        }

                        // kalau field multiple JSON
                        if (is_array($record->images ?? null)) {
                            foreach ($record->images as $file) {
                                Storage::disk('public')->delete($file);
                            }
                        }
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function ($records) {
                            foreach ($records as $record) {
                                // Hapus single image
                                if ($record->image) {
                                    Storage::disk('public')->delete($record->image);
                                }

                                // Kalau field multiple JSON
                                if (is_array($record->images ?? null)) {
                                    foreach ($record->images as $file) {
                                        Storage::disk('public')->delete($file);
                                    }
                                }
                            }
                        }),
                ]),
            ]);
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
            'index' => Pages\ListTvGaleris::route('/'),
            'create' => Pages\CreateTvGaleri::route('/create'),
            'edit' => Pages\EditTvGaleri::route('/{record}/edit'),
        ];
    }
}
