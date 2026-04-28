<?php

namespace App\Filament\Resources\UploadDokumens;

use App\Filament\Resources\UploadDokumens\Schemas\UploadDokumenForm;
use App\Filament\Resources\UploadDokumens\Tables\UploadDokumensTable;
use App\Models\UploadDokumen;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class UploadDokumenResource extends Resource
{
    protected static ?string $model = UploadDokumen::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud-arrow-up';

    protected static ?string $navigationLabel = 'Upload Dokumen';

    protected static string|\UnitEnum|null $navigationGroup = 'Manajemen Surat';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $form): Schema
    {
        return UploadDokumenForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return UploadDokumensTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUploadDokumens::route('/'),
            'create' => Pages\CreateUploadDokumen::route('/create'),
            // 'view' => Pages\ViewUploadDokumen::route('/{record}'),
            'edit' => Pages\EditUploadDokumen::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_verified', false)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
