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
        Schema::create('jenis_surat', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nama_jenis');
            $table->string('kode')->nullable();
            $table->string('url_surat');
            $table->string('kode_surat', 10)->nullable();
            $table->string('lampiran')->nullable();
            $table->boolean('mandiri')->default(0);
            $table->text('template')->nullable();
            $table->text('template_desa')->nullable();
            $table->text('form_isian')->nullable();
            $table->text('kode_isian')->nullable();
            $table->string('syarat_surat')->nullable();
            $table->string('orientasi')->nullable();
            $table->string('ukuran')->nullable();
            $table->string('margin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_surat');
    }
};
