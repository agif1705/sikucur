<?php

namespace Database\Seeders;

use App\Models\Penduduk;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PendudukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = file_get_contents(database_path('seeders/penduduk.json'));
        $data = json_decode($json, true);

        foreach ($data as $row) {
            // Normalisasi kolom
            $tanggalLahir = null;
            if (!empty($row['tanggal lahir'])) {
                // Gunakan Carbon untuk pastikan format
                $tanggalLahir = Carbon::parse($row['tanggal lahir'])->format('Y-m-d');
            }
            $jk = $row['jk'] === 'L' ? 1 : 2;
            Penduduk::create([
                'nagari_id'      => $row['nagari_id'],
                'name'           => strtoupper($row['name']),
                'nik'            => (string) $row['nik'],
                'alamat'         => ucfirst(strtolower($row['alamat'])),
                'jk'             => $jk,
                'tempat_lahir'   => $row['tempat Lahir'] ?? null,
                'tanggal_lahir'  => $tanggalLahir,
                'korong'         => $row['korong'] ?? null,
                'kk'             => (string) $row['kk'],
            ]);
        }
    }
}
