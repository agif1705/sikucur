<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Penduduk;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Imports\PendudukImporter;
use App\Filament\Resources\PendudukResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PendudukResource\RelationManagers;
use App\Filament\Imports\ProductImporter;
use Filament\Tables\Actions\ImportAction;
use Filament\Tables\Columns\IconColumn;

class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('phone_mikrotik')
                    ->label('Nomor Telepon')
                    ->tel()
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                IconColumn::make('jk')->label('L/P')
                    ->icon(fn(string $state): string => match ($state) {
                        '1' => 'gmdi-male',
                        '2' => 'gmdi-female-o',
                    })->color(fn(string $state): string => match ($state) {
                        '1' => 'success',
                        '2' => 'warning',
                    }),
                TextColumn::make('nik')->searchable(),
                TextColumn::make('tempat_lahir')->searchable(),
                TextColumn::make('tanggal_lahir')->searchable(),
                TextColumn::make('alamat'),
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
            'index' => Pages\ListPenduduks::route('/'),
            'create' => Pages\CreatePenduduk::route('/create'),
            'edit' => Pages\EditPenduduk::route('/{record}/edit'),
        ];
    }
}