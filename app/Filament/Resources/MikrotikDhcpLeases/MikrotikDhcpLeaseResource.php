<?php

namespace App\Filament\Resources\MikrotikDhcpLeases;

use App\Filament\Resources\MikrotikDhcpLeases\Schemas\MikrotikDhcpLeaseForm;
use App\Filament\Resources\MikrotikDhcpLeases\Tables\MikrotikDhcpLeasesTable;
use App\Models\MikrotikDhcpLease;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MikrotikDhcpLeaseResource extends Resource
{
    protected static ?string $model = MikrotikDhcpLease::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-router';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotspot Sikucur';

    protected static ?string $modelLabel = 'DHCP Lease';

    protected static ?string $pluralModelLabel = 'DHCP Leases';

    public static function form(Schema $form): Schema
    {
        return MikrotikDhcpLeaseForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return MikrotikDhcpLeasesTable::configure($table);
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
            'index' => Pages\ListMikrotikDhcpLeases::route('/'),
            'create' => Pages\CreateMikrotikDhcpLease::route('/create'),
            'edit' => Pages\EditMikrotikDhcpLease::route('/{record}/edit'),
        ];
    }
}
