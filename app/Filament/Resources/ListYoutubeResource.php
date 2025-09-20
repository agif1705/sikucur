<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListYoutubeResource\Pages;
use App\Filament\Resources\ListYoutubeResource\RelationManagers;
use App\Models\ListYoutube;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListYoutubeResource extends Resource
{
    protected static ?string $model = ListYoutube::class;

    protected static ?string $navigationGroup = 'Tv Informasi';
    protected static ?string $navigationLabel = 'List Youtube';
    protected static ?string $navigationIcon = 'heroicon-o-play-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('url')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('id_youtube')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_youtube')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListListYoutubes::route('/'),
            'create' => Pages\CreateListYoutube::route('/create'),
            'edit' => Pages\EditListYoutube::route('/{record}/edit'),
        ];
    }
}