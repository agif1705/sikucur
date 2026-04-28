<?php

namespace App\Filament\Resources\SuratKepalas;

use App\Filament\Resources\SuratKepalas\Schemas\SuratKepalaForm;
use App\Filament\Resources\SuratKepalas\Tables\SuratKepalasTable;
use App\Models\SuratKepala;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SuratKepalaResource extends Resource
{
    protected static ?string $model = SuratKepala::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Surat';

    public static function form(Schema $form): Schema
    {
        return SuratKepalaForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return SuratKepalasTable::configure($table);
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
            'index' => Pages\ListSuratKepalas::route('/'),
            'create' => Pages\CreateSuratKepala::route('/create'),
            'edit' => Pages\EditSuratKepala::route('/{record}/edit'),
        ];
    }
}
