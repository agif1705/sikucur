<?php

namespace App\Filament\Resources\AbsensiWebPegawais\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class AbsensiWebPegawaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('user_id', Auth::id()))
            ->columns([
                Tables\Columns\TextColumn::make('user.username')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('absensi')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('file_pendukung')
                    ->disk('public')
                    ->square(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alasan')
                    ->searchable(),
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
