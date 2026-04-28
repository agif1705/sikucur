<?php

namespace App\Filament\Resources\Penduduks;

use App\Filament\Resources\Penduduks\Pages\CreatePenduduk;
use App\Filament\Resources\Penduduks\Pages\EditPenduduk;
use App\Filament\Resources\Penduduks\Pages\ListPenduduks;
use App\Filament\Resources\Penduduks\Schemas\PendudukForm;
use App\Filament\Resources\Penduduks\Tables\PenduduksTable;
use App\Models\Penduduk;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-people-tt';

    protected static string|\UnitEnum|null $navigationGroup = 'Data';

    protected static ?string $navigationLabel = 'Penduduk';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return PendudukForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return PenduduksTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPenduduks::route('/'),
            'create' => CreatePenduduk::route('/create'),
            'edit' => EditPenduduk::route('/{record}/edit'),
        ];
    }
}
