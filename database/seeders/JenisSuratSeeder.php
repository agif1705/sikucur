<?php

namespace Database\Seeders;

use App\Models\JenisSurat;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\DokumenPersyaratan;

class JenisSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/JenisSurat.json'));
        $data = json_decode($json, true);
        foreach ($data as $item) {
            $surat = JenisSurat::create([
                'nama' => $item['nama'],
                'nama_jenis' => Str::of($item['url_surat'])
                    ->replace('sistem-', '')
                    ->replace('-', ' '),
                'kode' =>  collect(
                    explode('-', $item['url_surat'])
                )
                    ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                    ->implode(''),
                'url_surat' => $item['url_surat'],
                'kode_surat' => $item['kode_surat'],
                'lampiran' => $item['lampiran'],
                'mandiri' => $item['mandiri'],
                'template' => $item['template'],
                'template_desa' => $item['template_desa'],
                'form_isian' => $item['form_isian'],
                'kode_isian' => $item['kode_isian'],
                'orientasi' => $item['orientasi'],
                'ukuran' => $item['ukuran'],
                'syarat_surat' => $item['syarat_surat'],
                'margin' => $item['margin']
            ]);
        }
    }
}
