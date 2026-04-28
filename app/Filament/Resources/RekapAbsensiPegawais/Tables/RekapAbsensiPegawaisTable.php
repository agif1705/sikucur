<?php

namespace App\Filament\Resources\RekapAbsensiPegawais\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class RekapAbsensiPegawaisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $is_super_admin = Auth::user()->hasRole('super_admin');
                $currentMonth = now()->month;
                $currentYear = now()->year;

                if (! $is_super_admin) {
                    $query->where('user_id', Auth::user()->id);
                    // ->whereMonth('date', $currentMonth)
                    // ->whereYear('date', $currentYear);
                } else {
                    $query->whereMonth('date', $currentMonth)
                        ->whereYear('date', $currentYear);
                }

                return $query->with(['user']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Pegawai')
                    ->numeric()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nagari.name')
                    ->label('Nagari')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_late')
                    ->label('Tepat Waktu')
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-check-badge')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status_absensi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('resource')
                    ->searchable(),
                Tables\Columns\TextColumn::make('id_resource')
                    ->searchable(),
                Tables\Columns\TextColumn::make('time_in'),
                Tables\Columns\TextColumn::make('time_out'),
                Tables\Columns\TextColumn::make('date')
                    ->searchable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
            ])
            ->actions([
                EditAction::make(),
            ])->headerActions([
                Action::make('create')
                    ->label('Sinkron FingerPrint Bulan '.now()->format('F Y'))
                    ->action(fn () => static::sinkronFingerPrint())
                    ->button()
                    ->icon('heroicon-o-plus')
                    ->visible(fn () => Auth::user()->hasRole('super_admin')),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
