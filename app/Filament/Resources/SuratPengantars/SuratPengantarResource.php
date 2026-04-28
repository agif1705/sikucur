<?php

namespace App\Filament\Resources\SuratPengantars;

use App\Filament\Resources\SuratPengantars\Schemas\SuratPengantarForm;
use App\Filament\Resources\SuratPengantars\Tables\SuratPengantarsTable;
use App\Models\SuratPengantar;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SuratPengantarResource extends Resource
{
    protected static ?string $model = SuratPengantar::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Surat Pengantar';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Surat';

    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return static::canManagePelayanan();
    }

    public static function form(Schema $form): Schema
    {
        return SuratPengantarForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return SuratPengantarsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuratPengantars::route('/'),
            'create' => Pages\CreateSuratPengantar::route('/create'),
            'edit' => Pages\EditSuratPengantar::route('/{record}/edit'),
        ];
    }

    private static function canManagePelayanan(): bool
    {
        $user = Auth::user();

        return $user?->hasAnyRole(['super_admin', 'Kasi Pelayanan', 'Staf Pelayanan']) === true;
    }
}
