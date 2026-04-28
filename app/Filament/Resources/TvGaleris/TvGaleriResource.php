<?php

namespace App\Filament\Resources\TvGaleris;

use App\Filament\Resources\TvGaleris\Schemas\TvGaleriForm;
use App\Filament\Resources\TvGaleris\Tables\TvGalerisTable;
use App\Models\TvGaleri;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TvGaleriResource extends Resource
{
    protected static ?string $model = TvGaleri::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Tv Informasi';

    protected static ?string $navigationLabel = 'Galeri TV';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tv';

    public static function form(Schema $form): Schema
    {
        return TvGaleriForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return TvGalerisTable::configure($table);
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
            'index' => Pages\ListTvGaleris::route('/'),
            'create' => Pages\CreateTvGaleri::route('/create'),
            'edit' => Pages\EditTvGaleri::route('/{record}/edit'),
        ];
    }
}
