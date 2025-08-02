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
        Schema::create('absensi_pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('absensi_by')->nullable();
            $table->unsignedInteger('emp_id');
            $table->enum('absensi', ['Hadir', 'Hadir Dinas Luar Daerah', 'Hadir Dinas Dalam Daerah',  'Sakit', 'Cuti', 'Alpha', 'Izin'])->nullable();
            $table->enum('status_absensi', ['Ontime', 'Terlambat', 'Pulang Cepat', 'HDL'])->nullable();
            $table->string('sn_mesin')->nullable();
            $table->boolean('accept')->nullable();
            $table->string('accept_by')->nullable();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nagari_id')->constrained('nagaris')->cascadeOnDelete();
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->date('date_in');
            $table->date('date_out')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawais');
    }
};
