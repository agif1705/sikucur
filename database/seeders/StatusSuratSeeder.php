<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusSurat;

class StatusSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'nama_status' => 'Permohonan Masuk',
                'kode_status' => 'MASUK',
                'warna_status' => 'primary',
                'deskripsi' => 'Permohonan baru masuk, menunggu verifikasi',
                'urutan' => 1
            ],
            [
                'nama_status' => 'Verifikasi Dokumen',
                'kode_status' => 'VERIF',
                'warna_status' => 'warning',
                'deskripsi' => 'Sedang melakukan verifikasi kelengkapan dokumen',
                'urutan' => 2
            ],
            [
                'nama_status' => 'Proses Pembuatan',
                'kode_status' => 'PROSES',
                'warna_status' => 'info',
                'deskripsi' => 'Sedang dalam proses pembuatan surat',
                'urutan' => 3
            ],
            [
                'nama_status' => 'Menunggu Tanda Tangan',
                'kode_status' => 'TTD',
                'warna_status' => 'secondary',
                'deskripsi' => 'Menunggu tanda tangan pejabat',
                'urutan' => 4
            ],
            [
                'nama_status' => 'Selesai',
                'kode_status' => 'SELESAI',
                'warna_status' => 'success',
                'deskripsi' => 'Surat sudah selesai dan siap diambil',
                'urutan' => 5
            ],
            [
                'nama_status' => 'Ditolak',
                'kode_status' => 'TOLAK',
                'warna_status' => 'danger',
                'deskripsi' => 'Permohonan ditolak karena tidak memenuhi syarat',
                'urutan' => 6
            ]
        ];

        foreach ($statuses as $status) {
            StatusSurat::create($status);
        }
    }
}