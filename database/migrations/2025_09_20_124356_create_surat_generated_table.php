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
        Schema::create('surat_generated', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_surat');
            $table->string('nomor_surat')->unique();
            $table->string('file_path');
            $table->string('qr_code_path')->nullable();
            $table->date('tanggal_terbit');
            $table->date('berlaku_sampai')->nullable();
            $table->string('ditandatangani_oleh');
            $table->string('jabatan_penandatangan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_generated');
    }
};
