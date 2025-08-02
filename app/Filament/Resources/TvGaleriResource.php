<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TvGaleriResource\Pages;
use App\Filament\Resources\TvGaleriResource\RelationManagers;
use App\Models\TvGaleri;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                        ->image(),

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

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
