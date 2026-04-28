<?php

namespace App\Filament\Resources\Nagaris;

use App\Filament\Resources\Nagaris\Schemas\NagariForm;
use App\Filament\Resources\Nagaris\Tables\NagarisTable;
use App\Models\Nagari;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class NagariResource extends Resource
{
    protected static ?string $model = Nagari::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Pengaturan Pegawai';

    public static function form(Schema $form): Schema
    {
        return NagariForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return NagarisTable::configure($table);
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
            'index' => Pages\ListNagaris::route('/'),
            'create' => Pages\CreateNagari::route('/create'),
            'edit' => Pages\EditNagari::route('/{record}/edit'),
        ];
    }
}
