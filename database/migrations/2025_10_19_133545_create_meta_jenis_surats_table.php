<?php
// filepath: database/migrations/xxxx_create_meta_jenis_surats_table.php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meta_jenis_surats', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description');
            $table->string('category')->default('pemohon');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default meta
        DB::table('meta_jenis_surats')->insert([
            // Data Pemohon
            ['name' => '[NAMA]', 'description' => 'Nama lengkap pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[NIK]', 'description' => 'NIK pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[ALAMAT_LENGKAP]', 'description' => 'Alamat lengkap pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[JK]', 'description' => 'Jenis kelamin pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[TEMPAT_LAHIR]', 'description' => 'Tempat lahir pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[TANGGAL_LAHIR]', 'description' => 'Tanggal lahir pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[TELEPON]', 'description' => 'Nomor telepon pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[NO_KK]', 'description' => 'Nomor Kartu Keluarga pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[KEPALA_KK]', 'description' => 'Kepala Keluarga pemohon', 'category' => 'pemohon', 'created_at' => now(), 'updated_at' => now()],

            // Data Surat
            ['name' => '[NOMOR_SURAT]', 'description' => 'Nomor surat lengkap', 'category' => 'surat', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[JUDUL_SURAT]', 'description' => 'Judul surat', 'category' => 'surat', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[TGL_SURAT]', 'description' => 'Tanggal pembuatan surat', 'category' => 'surat', 'created_at' => now(), 'updated_at' => now()],
            // Data Nagari
            ['name' => '[SEBUTAN_DESA]', 'description' => 'Sebutan nagari/desa', 'category' => 'nagari', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[NAMA_DESA]', 'description' => 'Nama nagari/desa', 'category' => 'nagari', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[NAMA_KECAMATAN]', 'description' => 'Nama kecamatan', 'category' => 'nagari', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[NAMA_KABUPATEN]', 'description' => 'Nama kabupaten', 'category' => 'nagari', 'created_at' => now(), 'updated_at' => now()],

            // Data Pejabat
            ['name' => '[JABATAN]', 'description' => 'Jabatan penandatangan surat', 'category' => 'pejabat', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '[NAMA_PEJABAT]', 'description' => 'Nama penandatangan surat', 'category' => 'pejabat', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('meta_jenis_surats');
    }
};
