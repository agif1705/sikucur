<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WhatsAppCommandResource\Pages;
use App\Filament\Resources\WhatsAppCommandResource\RelationManagers;
use App\Models\WhatsAppCommand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WhatsAppCommandResource extends Resource
{
    protected static ?string $model = WhatsAppCommand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Broadcast & Notifikasi';


    public static function form(Form $form): Form
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nagari.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('command')
                    ->searchable(),
                Tables\Columns\TextColumn::make('handler_class')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
