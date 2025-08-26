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
        Schema::create('absensi_web_pegawais', function (Blueprint $table) {
            $table->id();
            $table->enum('absensi', ['HDLD', 'HDDD', 'S', 'C', 'I'])->nullable();
            $table->boolean('is_late')->default(false);
            $table->string('file_pendukung')->default(false);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('nagari_id')->constrained('nagaris')->cascadeOnDelete();
            $table->time('time_in');
            $table->time('time_out')->nullable();
            $table->date('date');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_web_pegawais');
    }
};
