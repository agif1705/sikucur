<?php

namespace App\Filament\Resources\MikrotikQueueDhcpTrackings;

use App\Filament\Resources\MikrotikQueueDhcpTrackings\Pages\ListMikrotikQueueDhcpTrackings;
use App\Filament\Resources\MikrotikQueueDhcpTrackings\Tables\MikrotikQueueDhcpTrackingsTable;
use App\Models\MikrotikQueueDhcpTracking;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MikrotikQueueDhcpTrackingResource extends Resource
{
    protected static ?string $model = MikrotikQueueDhcpTracking::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-compare-arrows';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotspot Sikucur';

    protected static ?string $modelLabel = 'Tracking DHCP Queue';

    protected static ?string $pluralModelLabel = 'Tracking DHCP Queue';

    public static function table(Table $table): Table
    {
        return MikrotikQueueDhcpTrackingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMikrotikQueueDhcpTrackings::route('/'),
        ];
    }
}
