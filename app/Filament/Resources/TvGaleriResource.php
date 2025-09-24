<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\TvGaleri;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TvGaleriResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TvGaleriResource\RelationManagers;

class TvGaleriResource extends Resource
{
    protected static ?string $model = TvGaleri::class;

    protected static ?string $navigationGroup = 'Tv Informasi';
    protected static ?string $navigationLabel = 'Galeri TV';
    protected static ?string $navigationIcon = 'heroicon-o-tv';

    public static function form(Form $form): Form
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
                            $ext  = $file->getClientOriginalExtension(); // ambil extensi asli

                            return "galeri-{$date}-{$uuid}.{$ext}";
                        })->deleteUploadedFileUsing(function ($file) {
                            Storage::disk('public')->delete($file);
                        }),

                ])
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
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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