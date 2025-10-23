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
        Schema::create('permohonan_surats', function (Blueprint $table) {
            $table->id();

            // Nomor dan identifikasi
            $table->string('nomor_permohonan')->unique();
            $table->integer('nomor')->nullable();

            // Relasi
            $table->foreignId('jenis_surat_id')->constrained('jenis_surat')->cascadeOnDelete();
            $table->foreignId('nagari_id')->constrained('nagaris')->cascadeOnDelete();
            $table->foreignId('status_id')->default(1)->constrained('status_surat');
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('penduduk_id')->nullable()->constrained('penduduks')->nullOnDelete(); // Relasi ke penduduk

            // Tanggal
            $table->timestamp('tanggal_permohonan')->useCurrent();
            $table->date('tanggal_surat')->nullable();
            $table->timestamp('tanggal_estimasi_selesai')->nullable();

            // Data Pemohon
            $table->string('pemohon_nik', 16);
            $table->string('pemohon_nama');
            $table->text('pemohon_alamat');
            $table->string('pemohon_telepon')->nullable();
            $table->string('pemohon_email')->nullable();
            $table->string('pemohon_jk', 20)->nullable();
            $table->string('pemohon_tempat_lahir')->nullable();
            $table->date('pemohon_tanggal_lahir')->nullable();
            $table->string('pemohon_agama')->nullable();

            // Template dan Form Data
            $table->longText('pemohon_template')->nullable();
            $table->string('pemohon_judul_surat')->nullable();
            $table->string('pemohon_kode_surat')->nullable();
            $table->json('form_data')->nullable();

            // Pejabat Penanda Tangan
            $table->string('PejabatTandaTangan_nama')->nullable();
            $table->string('PejabatTandaTangan_jabatan')->nullable();
            $table->foreignId('TandaTangan')->nullable()->constrained('users')->nullOnDelete();

            // Keperluan dan Catatan
            $table->text('keperluan')->nullable();
            $table->text('catatan_petugas')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['nomor_permohonan', 'jenis_surat_id', 'status_id', 'tanggal_permohonan']);
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
