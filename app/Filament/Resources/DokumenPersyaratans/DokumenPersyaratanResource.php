<?php

namespace App\Filament\Resources\DokumenPersyaratans;

use App\Filament\Resources\DokumenPersyaratans\Schemas\DokumenPersyaratanForm;
use App\Filament\Resources\DokumenPersyaratans\Tables\DokumenPersyaratansTable;
use App\Models\DokumenPersyaratan;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DokumenPersyaratanResource extends Resource
{
    protected static ?string $model = DokumenPersyaratan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-check';

    protected static ?string $navigationLabel = 'Dokumen Persyaratan';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Surat';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $form): Schema
    {
        return DokumenPersyaratanForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return DokumenPersyaratansTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokumenPersyaratans::route('/'),
            'create' => Pages\CreateDokumenPersyaratan::route('/create'),
            // 'view' => Pages\ViewDokumenPersyaratan::route('/{record}'),
            'edit' => Pages\EditDokumenPersyaratan::route('/{record}/edit'),
        ];
    }
}
