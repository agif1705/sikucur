<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\JenisSurat;
use App\Models\MetaJenisSurat;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class SuratSetting extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.surat-setting';

    public function mount(): void
    {
        $jenisSurat = JenisSurat::all();
        $metaJenisSurat = MetaJenisSurat::all();

        $semuaArray = [];
        $perSuratData = [];

        foreach ($jenisSurat as $surat) {
            // Ambil semua teks dalam [ ... ]
            preg_match_all('/\[(.*?)\]/', $surat->template, $matches);
            $array = array_unique($matches[1]);

            // Hapus QR_CODE (case-insensitive)
            $array = array_filter($array, fn($v) => strtoupper($v) !== 'QR_CODE');

            // Ubah ke huruf besar
            $arrayUpper = array_map('strtoupper', $array);
            // Gabung semua
            $semuaArray = array_merge($semuaArray, $arrayUpper);
            $perSuratData[$surat->id] = $arrayUpper;
        }

        // Hapus duplikat global
        $unikSemua = array_unique($semuaArray);
        dd($unikSemua);
        // Hitung unik per surat
        $perSuratUnik = [];
        foreach ($perSuratData as $id => $arr) {
            $lainnya = array_diff($semuaArray, $arr);
            $unik = array_diff($arr, $lainnya);
            $perSuratUnik[$id] = $unik;
        }

        // === Ubah isi template ===
        // foreach ($jenisSurat as $surat) {
        //     $template = $surat->template;

        //     // Hapus [QR_CODE] (case-insensitive)
        //     $template = preg_replace('/\[QR_CODE\]/i', '', $template);

        //     // Ambil ulang semua teks dalam [ ... ]
        //     preg_match_all('/\[(.*?)\]/', $template, $matches);

        //     // Ubah semuanya ke uppercase
        //     foreach ($matches[1] as $original) {
        //         $upper = strtoupper($original);
        //         $template = str_replace("[$original]", "[$upper]", $template);
        //     }

        //     // Simpan hasil HTML baru
        //     $surat->update([
        //         'template' => $template,
        //     ]);
        // }

        // Debug hasil
        dd([
            'semua' => $semuaArray,
            'unik_global' => $unikSemua,
            'unik_per_surat' => $perSuratUnik,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(JenisSurat::query())
            ->columns([
                TextColumn::make('nama_jenis')->label('Nama Jenis Surat')->sortable()->searchable(),
                TextColumn::make('template_desa')->label('template')->sortable()->searchable()->wrap(),
            ]);
    }
}
