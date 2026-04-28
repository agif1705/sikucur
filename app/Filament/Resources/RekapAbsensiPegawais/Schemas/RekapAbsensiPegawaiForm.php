<?php

namespace App\Filament\Resources\RekapAbsensiPegawais\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;

class RekapAbsensiPegawaiForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('nagari_id')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('is_late')
                    ->required(),
                Forms\Components\TextInput::make('status_absensi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('sn_mesin')
                    ->maxLength(255),
                Forms\Components\TextInput::make('resource')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('id_resource')
                    ->maxLength(255),
                Forms\Components\TextInput::make('time_in')
                    ->required(),
                Forms\Components\TextInput::make('time_out'),
                Forms\Components\DatePicker::make('date')
                    ->required(),
            ]);
    }
}
