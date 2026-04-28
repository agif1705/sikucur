<?php

namespace App\Filament\Resources\TvInformasis;

use App\Filament\Resources\TvInformasis\Schemas\TvInformasiForm;
use App\Filament\Resources\TvInformasis\Tables\TvInformasisTable;
use App\Models\TvInformasi;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TvInformasiResource extends Resource
{
    protected static ?string $model = TvInformasi::class;

    protected static string|\UnitEnum|null $navigationGroup = 'Tv Informasi';

    protected static ?string $navigationLabel = 'Data Tv Informasi';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-c-tv';

    public static function form(Schema $form): Schema
    {
        return TvInformasiForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return TvInformasisTable::configure($table);
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
            'index' => Pages\ListTvInformasis::route('/'),
            'create' => Pages\CreateTvInformasi::route('/create'),
            'edit' => Pages\EditTvInformasi::route('/{record}/edit'),
        ];
    }
}
