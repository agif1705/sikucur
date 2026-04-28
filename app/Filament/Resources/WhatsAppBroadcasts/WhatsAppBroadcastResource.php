<?php

namespace App\Filament\Resources\WhatsAppBroadcasts;

use App\Filament\Resources\WhatsAppBroadcasts\Schemas\WhatsAppBroadcastForm;
use App\Filament\Resources\WhatsAppBroadcasts\Tables\WhatsAppBroadcastsTable;
use App\Models\WhatsAppBroadcast;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WhatsAppBroadcastResource extends Resource
{
    protected static ?string $model = WhatsAppBroadcast::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationLabel = 'WhatsApp Broadcast';

    protected static ?string $modelLabel = 'WhatsApp Broadcast';

    protected static ?string $pluralModelLabel = 'WhatsApp Broadcasts';

    protected static string|\UnitEnum|null $navigationGroup = 'Broadcast & Notifikasi';

    public static function form(Schema $form): Schema
    {
        return WhatsAppBroadcastForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return WhatsAppBroadcastsTable::configure($table);
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
            'index' => Pages\ListWhatsAppBroadcasts::route('/'),
            'create' => Pages\CreateWhatsAppBroadcast::route('/create'),
            'edit' => Pages\EditWhatsAppBroadcast::route('/{record}/edit'),
            'logs' => Pages\ViewBroadcastLogs::route('/{record}/logs'),
        ];
    }
}
