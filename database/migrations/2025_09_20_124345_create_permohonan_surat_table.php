<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permohonan_surat', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_permohonan')->unique();
            $table->foreignId('jenis_surat_id')->constrained('jenis_surat');
            $table->foreignId('nagari_id')->constrained('nagaris');
            
            // Data Pemohon
            $table->string('pemohon_nik', 16);
            $table->string('pemohon_nama');
            $table->text('pemohon_alamat');
            $table->string('pemohon_telepon')->nullable();
            $table->string('pemohon_email')->nullable();
            
            // Detail Permohonan
            $table->text('keperluan');
            $table->foreignId('status_id')->constrained('status_surat');
            
            // Tanggal
            $table->datetime('tanggal_permohonan');
            $table->datetime('tanggal_estimasi_selesai')->nullable();
            $table->datetime('tanggal_selesai')->nullable();
            
            // Petugas
            $table->foreignId('petugas_id')->nullable()->constrained('users');
            $table->text('catatan_petugas')->nullable();
            
            // Metadata
            $table->json('data_tambahan')->nullable(); // For flexible data
            $table->timestamps();
            
            $table->index(['pemohon_nik', 'nagari_id']);
            $table->index(['status_id', 'tanggal_permohonan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permohonan_surat');
    }
};
