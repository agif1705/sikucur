<?php

namespace App\Filament\Resources\PppSecrets;

use App\Filament\Resources\PppSecrets\Pages\ListPppSecrets;
use App\Filament\Resources\PppSecrets\Tables\PppSecretsTable;
use App\Models\MikrotikConfig;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PppSecretResource extends Resource
{
    protected static ?string $model = MikrotikConfig::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-vpn-key';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotspot Sikucur';

    protected static ?string $modelLabel = 'PPP Secret';

    protected static ?string $pluralModelLabel = 'PPP Secrets';

    public static function table(Table $table): Table
    {
        return PppSecretsTable::configure($table);
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
            'index' => ListPppSecrets::route('/'),
        ];
    }
}
