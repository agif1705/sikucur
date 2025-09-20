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
        Schema::create('tracking_surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permohonan_id')->constrained('permohonan_surat');
            $table->foreignId('status_lama_id')->nullable()->constrained('status_surat');
            $table->foreignId('status_baru_id')->constrained('status_surat');
            $table->foreignId('petugas_id')->constrained('users');
            $table->datetime('tanggal_perubahan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_surat');
    }
};
