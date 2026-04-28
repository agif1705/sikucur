<?php

namespace App\Filament\Resources\WhatsAppCommands\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class WhatsAppCommandForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('footer_whats_app_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Select::make('nagari_id')
                    ->relationship('nagari', 'name')
                    ->required(),
                Forms\Components\TextInput::make('command')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('handler_class')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
