<?php

namespace App\Filament\Resources\RekapAbsensiPegawais;

use App\Filament\Resources\RekapAbsensiPegawais\Schemas\RekapAbsensiPegawaiForm;
use App\Filament\Resources\RekapAbsensiPegawais\Tables\RekapAbsensiPegawaisTable;
use App\Models\Nagari;
use App\Models\RekapAbsensiPegawai;
use App\Services\SinkronFingerprintService;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class RekapAbsensiPegawaiResource extends Resource
{
    protected static ?string $model = RekapAbsensiPegawai::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static string|\UnitEnum|null $navigationGroup = 'Absensi';

    protected static ?string $navigationLabel = 'Rekap Absensi Bulanan';

    public static function form(Schema $form): Schema
    {
        return RekapAbsensiPegawaiForm::configure($form);
    }

    public static function table(Table $table): Table
    {
        return RekapAbsensiPegawaisTable::configure($table);
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
            'index' => Pages\ListRekapAbsensiPegawais::route('/'),
            // 'create' => Pages\CreateRekapAbsensiPegawai::route('/create'),
            // 'edit' => Pages\EditRekapAbsensiPegawai::route('/{record}/edit'),
        ];
    }

    public static function sinkronFingerPrint(): void
    {
        $user = Auth::user();

        if (! $user?->nagari) {
            return;
        }

        SinkronFingerprintService::sinkronFingerPrint(Nagari::find($user->nagari->id));
    }
}
