<?php

namespace App\Filament\Resources\MikrotikQueues;

use App\Filament\Resources\MikrotikQueues\Pages\ListMikrotikQueues;
use App\Filament\Resources\MikrotikQueues\Tables\MikrotikQueuesTable;
use App\Models\MikrotikQueue;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class MikrotikQueueResource extends Resource
{
    protected static ?string $model = MikrotikQueue::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-speed';

    protected static string|\UnitEnum|null $navigationGroup = 'Hotspot Sikucur';

    protected static ?string $modelLabel = 'Queue MikroTik';

    protected static ?string $pluralModelLabel = 'Queues MikroTik';

    public static function table(Table $table): Table
    {
        return MikrotikQueuesTable::configure($table);
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
            'index' => ListMikrotikQueues::route('/'),
        ];
    }
}
