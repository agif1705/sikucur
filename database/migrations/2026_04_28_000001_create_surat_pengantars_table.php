<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_pengantars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nagari_id')->constrained('nagaris')->cascadeOnDelete();
            $table->foreignId('penduduk_id')->nullable()->constrained('penduduks')->nullOnDelete();
            $table->foreignId('petugas_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('wali_korong_id')->nullable()->constrained('wali_korongs')->nullOnDelete();

            $table->string('token')->unique();
            $table->string('status')->default('draft');
            $table->boolean('used')->default(false);
            $table->timestamp('expired_at')->nullable()->index();
            $table->date('tanggal_pengantar')->nullable();

            $table->string('pemohon_nik', 16)->nullable();
            $table->string('pemohon_nama')->nullable();
            $table->text('pemohon_alamat')->nullable();
            $table->string('pemohon_telepon')->nullable();
            $table->string('korong')->nullable();
            $table->text('keperluan')->nullable();

            $table->json('form_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengantars');
    }
};
