<?php

namespace App\Filament\Resources\WhatsAppCommands;

use App\Filament\Resources\WhatsAppCommands\Schemas\WhatsAppCommandForm;
use App\Filament\Resources\WhatsAppCommands\Tables\WhatsAppCommandsTable;
use App\Models\WhatsAppCommand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WhatsAppCommandResource extends Resource
{
    protected static ?string $model = WhatsAppCommand::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Broadcast & Notifikasi';

    public static function form(Schema $form): Schema
    {
        return WhatsAppCommandForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return WhatsAppCommandsTable::configure($table);
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
            'index' => Pages\ListWhatsAppCommands::route('/'),
            'create' => Pages\CreateWhatsAppCommand::route('/create'),
            'edit' => Pages\EditWhatsAppCommand::route('/{record}/edit'),
        ];
    }
}
