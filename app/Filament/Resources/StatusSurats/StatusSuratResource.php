<?php

namespace App\Filament\Resources\StatusSurats;

use App\Filament\Resources\StatusSurats\Schemas\StatusSuratForm;
use App\Filament\Resources\StatusSurats\Tables\StatusSuratsTable;
use App\Models\StatusSurat;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class StatusSuratResource extends Resource
{
    protected static ?string $model = StatusSurat::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = 'Status Surat';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Surat';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return StatusSuratForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return StatusSuratsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStatusSurats::route('/'),
            'create' => Pages\CreateStatusSurat::route('/create'),
            // 'view' => Pages\ViewStatusSurat::route('/{record}'),
            'edit' => Pages\EditStatusSurat::route('/{record}/edit'),
        ];
    }
}
