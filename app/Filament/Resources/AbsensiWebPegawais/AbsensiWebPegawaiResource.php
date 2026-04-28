<?php

namespace App\Filament\Resources\AbsensiWebPegawais;

use App\Filament\Resources\AbsensiWebPegawais\Schemas\AbsensiWebPegawaiForm;
use App\Filament\Resources\AbsensiWebPegawais\Tables\AbsensiWebPegawaisTable;
use App\Models\AbsensiWebPegawai;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class AbsensiWebPegawaiResource extends Resource
{
    protected static ?string $model = AbsensiWebPegawai::class;

    protected static string|\BackedEnum|null $navigationIcon = 'gmdi-whatsapp-o';

    protected static string|\UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?string $heade = 'Absensi';

    protected static ?string $navigationLabel = 'Rekap Absensi WhatsApp';

    public static function form(Schema $form): Schema
    {
        return AbsensiWebPegawaiForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return AbsensiWebPegawaisTable::configure($table);
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
            'index' => Pages\ListAbsensiWebPegawais::route('/'),
            'create' => Pages\CreateAbsensiWebPegawai::route('/create'),
            'edit' => Pages\EditAbsensiWebPegawai::route('/{record}/edit'),
        ];
    }
}
