<?php

namespace App\Filament\Resources\HotspotSikucurs\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;

class HotspotSikucursTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('penduduk.nik')
                    ->sortable(),
                Tables\Columns\TextColumn::make('penduduk.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone_mikrotik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mikrotikConfig.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ret_id')
                    ->searchable(),
                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->label('Sts User')
                    ->sortable(),
                Tables\Columns\TextColumn::make('activated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->label('Modify'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
