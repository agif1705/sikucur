<?php

namespace App\Filament\Resources\HotspotSikucurs;

use App\Filament\Resources\HotspotSikucurs\Schemas\HotspotSikucurForm;
use App\Filament\Resources\HotspotSikucurs\Tables\HotspotSikucursTable;
use App\Models\HotspotSikucur;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class HotspotSikucurResource extends Resource
{
    protected static ?string $model = HotspotSikucur::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-network-cell';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotspot Sikucur';

    public static function form(Schema $form): Schema
    {
        return HotspotSikucurForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return HotspotSikucursTable::configure($table);
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
            'index' => Pages\ListHotspotSikucurs::route('/'),
            'create' => Pages\CreateHotspotSikucur::route('/create'),
            'edit' => Pages\EditHotspotSikucur::route('/{record}/edit'),
        ];
    }
}
