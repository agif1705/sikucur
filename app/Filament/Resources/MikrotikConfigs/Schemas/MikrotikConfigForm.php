<?php

namespace App\Filament\Resources\MikrotikConfigs\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class MikrotikConfigForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nagari')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('host')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('user')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('pass')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('port')
                    ->required()
                    ->numeric()
                    ->default(8728),
                Forms\Components\Toggle::make('ssl')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
