<?php

namespace App\Filament\Resources\SuratPengantars\Schemas;

use App\Models\JenisSurat;
use App\Models\Penduduk;
use App\Models\SuratPengantar;
use App\Models\WaliKorong;
use Filament\Forms;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class SuratPengantarForm
{
    public static function configure(Schema $form): Schema
    {
        return $form
            ->schema([
                Section::make('Data Pemohon')
                    ->schema([
                        Forms\Components\Select::make('penduduk_id')
                            ->label('Cari Data Penduduk')
                            ->searchable()
                            ->preload(false)
                            ->placeholder('Ketik minimal 3 karakter untuk mencari...')
                            ->options(fn () => [])
                            ->getSearchResultsUsing(function (string $search) {
                                if (strlen($search) < 3) {
                                    return [];
                                }

                                return Penduduk::query()
                                    ->where(function ($query) use ($search) {
                                        $query->where('nik', 'like', "%{$search}%")
                                            ->orWhere('name', 'ilike', "%{$search}%")
                                            ->orWhere('alamat', 'ilike', "%{$search}%")
                                            ->orWhere('alamat_domisili', 'ilike', "%{$search}%");
                                    })
                                    ->orderBy('name')
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(fn (Penduduk $penduduk) => [
                                        $penduduk->id => "{$penduduk->nik} | {$penduduk->name} | {$penduduk->alamat}",
                                    ]);
                            })
                            ->getOptionLabelUsing(function ($value): ?string {
                                $penduduk = Penduduk::find($value);

                                return $penduduk ? "{$penduduk->nik} | {$penduduk->name}" : null;
                            })
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (! $state) {
                                    return;
                                }

                                $penduduk = Penduduk::find($state);
                                if (! $penduduk) {
                                    return;
                                }

                                $set('pemohon_nik', $penduduk->nik);
                                $set('pemohon_nama', $penduduk->name);
                                $set('pemohon_alamat', $penduduk->alamat);
                                $set('pemohon_alamat_domisili', $penduduk->alamat_domisili);
                                $set('pemohon_telepon', $penduduk->no_hp);
                                $set('korong', $penduduk->korong);

                                $waliKorongId = WaliKorong::query()
                                    ->where('nagari_id', Auth::user()?->nagari_id)
                                    ->where('wilayah', $penduduk->korong)
                                    ->value('id');

                                if ($waliKorongId) {
                                    $set('wali_korong_id', $waliKorongId);
                                }
                            })
                            ->live()
                            ->helperText('Opsional. Data penduduk yang kurang lengkap tetap bisa dilengkapi manual.'),
                        Forms\Components\TextInput::make('pemohon_nik')
                            ->label('NIK')
                            ->required()
                            ->maxLength(16),
                        Forms\Components\TextInput::make('pemohon_nama')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('pemohon_alamat')
                            ->label('Alamat Lengkap Sesuai KTP')
                            ->required()
                            ->rows(3),
                        Forms\Components\Textarea::make('pemohon_alamat_domisili')
                            ->label('Alamat Lengkap Domisili')
                            ->rows(3)
                            ->helperText('Jika kosong, surat akan memakai alamat KTP.'),
                        Forms\Components\TextInput::make('pemohon_telepon')
                            ->label('Telepon')
                            ->maxLength(20),
                    ]),
                Section::make('Data Pengantar')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('korong')
                                    ->label('Korong/Wilayah')
                                    ->options(SuratPengantar::wilayahOptions())
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $set('wali_korong_id', WaliKorong::query()
                                            ->where('nagari_id', Auth::user()?->nagari_id)
                                            ->where('wilayah', $state)
                                            ->value('id'));

                                        $pendudukId = $get('penduduk_id');
                                        if (! $pendudukId || ! filled($state)) {
                                            return;
                                        }

                                        Penduduk::query()
                                            ->whereKey($pendudukId)
                                            ->where(function ($query) use ($state) {
                                                $query->whereNull('korong')
                                                    ->orWhere('korong', '<>', $state);
                                            })
                                            ->update(['korong' => $state]);
                                    }),
                                Forms\Components\DatePicker::make('tanggal_pengantar')
                                    ->label('Tanggal Surat')
                                    ->default(now()),
                            ]),
                        Forms\Components\Textarea::make('keperluan')
                            ->label('Keperluan')
                            ->required()
                            ->rows(3),
                        Forms\Components\Select::make('jenis_surat_id')
                            ->label('Jenis Surat Yang Akan Dibuat')
                            ->options(fn () => JenisSurat::query()->orderBy('nama_jenis')->pluck('nama_jenis', 'id')->all())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Select::make('wali_korong_id')
                            ->label('Wali Korong')
                            ->options(fn () => WaliKorong::query()
                                ->where('nagari_id', Auth::user()?->nagari_id)
                                ->get()
                                ->mapWithKeys(fn (WaliKorong $waliKorong) => [
                                    $waliKorong->id => "{$waliKorong->name} - {$waliKorong->wilayah}",
                                ])
                                ->all())
                            ->searchable()
                            ->required()
                            ->helperText('Otomatis mengikuti pilihan korong/wilayah.'),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(SuratPengantar::statusOptions())
                            ->default(SuratPengantar::STATUS_WAITING_APPROVAL),
                    ]),
            ]);
    }
}
