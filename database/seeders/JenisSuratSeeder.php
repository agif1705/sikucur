<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisSurat;
use App\Models\DokumenPersyaratan;

class JenisSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisSurat = [
            [
                'nama_jenis' => 'Surat Keterangan Domisili',
                'kode_surat' => 'SKD',
                'persyaratan' => 'KTP, KK, Pas Foto 3x4',
                'estimasi_hari' => 3,
                'keterangan' => 'Surat keterangan tempat tinggal',
                'dokumen' => [
                    'Fotocopy KTP',
                    'Fotocopy Kartu Keluarga',
                    'Pas Foto 3x4 (2 lembar)'
                ]
            ],
            [
                'nama_jenis' => 'Surat Pengantar KTP',
                'kode_surat' => 'SP-KTP',
                'persyaratan' => 'KK, Akta Lahir, Ijazah',
                'estimasi_hari' => 2,
                'keterangan' => 'Surat pengantar untuk pembuatan KTP baru',
                'dokumen' => [
                    'Fotocopy Kartu Keluarga',
                    'Fotocopy Akta Lahir',
                    'Fotocopy Ijazah Terakhir',
                    'Pas Foto 3x4 (4 lembar)'
                ]
            ],
            [
                'nama_jenis' => 'Surat Keterangan Tidak Mampu',
                'kode_surat' => 'SKTM',
                'persyaratan' => 'KTP, KK, Surat Keterangan RT/RW',
                'estimasi_hari' => 3,
                'keterangan' => 'Surat keterangan untuk keluarga tidak mampu',
                'dokumen' => [
                    'Fotocopy KTP Pemohon',
                    'Fotocopy Kartu Keluarga',
                    'Surat Keterangan RT/RW',
                    'Pas Foto 3x4 (2 lembar)'
                ]
            ],
            [
                'nama_jenis' => 'Surat Keterangan Usaha',
                'kode_surat' => 'SKU',
                'persyaratan' => 'KTP, KK, Foto Tempat Usaha',
                'estimasi_hari' => 5,
                'keterangan' => 'Surat keterangan memiliki usaha',
                'dokumen' => [
                    'Fotocopy KTP',
                    'Fotocopy Kartu Keluarga',
                    'Foto Tempat Usaha',
                    'Surat Keterangan RT/RW'
                ]
            ]
        ];

        foreach ($jenisSurat as $data) {
            $surat = JenisSurat::create([
                'nama_jenis' => $data['nama_jenis'],
                'kode_surat' => $data['kode_surat'],
                'persyaratan' => $data['persyaratan'],
                'estimasi_hari' => $data['estimasi_hari'],
                'keterangan' => $data['keterangan']
            ]);

            // Tambahkan dokumen persyaratan
            foreach ($data['dokumen'] as $index => $dokumen) {
                DokumenPersyaratan::create([
                    'jenis_surat_id' => $surat->id,
                    'nama_dokumen' => $dokumen,
                    'is_wajib' => true,
                    'urutan' => $index + 1
                ]);
            }
        }
    }
}
