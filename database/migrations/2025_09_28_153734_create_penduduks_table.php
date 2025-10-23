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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nagari_id')->constrained('nagaris');
            $table->string('name');
            $table->string('nik');
            $table->string('alamat');
            $table->tinyInteger('jk')->comment('1 = Laki-laki, 2 = Perempuan');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('korong');
            $table->string('negara')->default('Indonesia')->nullable();
            $table->string('agama')->default('Islam')->nullable();
            $table->string('status_kawin')->default('belum kawin')->nullable();
            $table->string('pekerjaan')->default('wiraswata')->nullable();
            $table->string('kk');
            $table->string('nama_kk')->nullable();
            $table->string('kepala_keluarga')->nullable();
            $table->string('no_hp')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penduduks');
    }
};
