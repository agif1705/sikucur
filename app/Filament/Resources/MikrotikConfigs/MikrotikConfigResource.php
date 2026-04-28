<?php

namespace App\Filament\Resources\MikrotikConfigs;

use App\Filament\Resources\MikrotikConfigs\Schemas\MikrotikConfigForm;
use App\Filament\Resources\MikrotikConfigs\Tables\MikrotikConfigsTable;
use App\Models\MikrotikConfig;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MikrotikConfigResource extends Resource
{
    protected static ?string $model = MikrotikConfig::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-network-check-tt';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotspot Sikucur';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $form): Schema
    {
        return MikrotikConfigForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return MikrotikConfigsTable::configure($table);
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
            'index' => Pages\ListMikrotikConfigs::route('/'),
            'create' => Pages\CreateMikrotikConfig::route('/create'),
            'edit' => Pages\EditMikrotikConfig::route('/{record}/edit'),
        ];
    }
}
