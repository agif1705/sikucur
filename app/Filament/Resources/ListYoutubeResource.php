<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\ListYoutube;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ListYoutubeResource\Pages;
use App\Filament\Resources\ListYoutubeResource\RelationManagers;

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
                TextInput::make('url')
                    ->label('YouTube URL')
                    ->required()
                    ->reactive()
                    ->hintIcon('heroicon-m-information-circle')
                    ->hint(function ($state) {
                        return empty($state)
                            ? 'Copy Paste URL dari YouTube Video kemudian simpan, ID YouTube akan terisi otomatis'
                            : null;
                    })->hintColor(fn($state) => empty($state) ? 'danger' : 'gray')->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            // Ambil ID dari url YouTube
                            $videoId = null;

                            // Pola standard ?v=
                            if (preg_match('/v=([a-zA-Z0-9_-]{11})/', $state, $matches)) {
                                $videoId = $matches[1];
                            }

                            // Pola shortlink youtu.be
                            if (preg_match('#youtu\.be/([a-zA-Z0-9_-]{11})#', $state, $matches)) {
                                $videoId = $matches[1];
                            }

                            // Pola embed
                            if (preg_match('#embed/([a-zA-Z0-9_-]{11})#', $state, $matches)) {
                                $videoId = $matches[1];
                            }

                            $set('id_youtube', $videoId);
                        }
                    }),

                TextInput::make('id_youtube')
                    ->label('YouTube ID')
                    ->required()
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('url')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_youtube')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort('created_at', 'desc')
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