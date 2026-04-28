<?php

namespace App\Filament\Resources\JenisSurats;

use App\Filament\Resources\JenisSurats\Schemas\JenisSuratForm;
use App\Filament\Resources\JenisSurats\Tables\JenisSuratsTable;
use App\Models\JenisSurat;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class JenisSuratResource extends Resource
{
    protected static ?string $model = JenisSurat::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $form): Schema
    {
        return JenisSuratForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return JenisSuratsTable::configure($table);
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
            'index' => Pages\ListJenisSurats::route('/'),
            'create' => Pages\CreateJenisSurat::route('/create'),
            'edit' => Pages\EditJenisSurat::route('/{record}/edit'),
        ];
    }
}
