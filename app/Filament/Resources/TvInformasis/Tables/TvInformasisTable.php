<?php

namespace App\Filament\Resources\TvInformasis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class TvInformasisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bamus')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('bamus_image')
                    ->square(),
                Tables\Columns\TextColumn::make('babinsa')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('babinsa_image')
                    ->square(),

            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
