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
        Schema::create('tv_informasis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('nagari_id')->constrained('nagaris');
            $table->foreignId('user_id')->constrained('users');
            $table->string('video')->nullable();
            $table->string('bupati')->nullable();
            $table->string('bupati_image')->nullable();
            $table->string('wakil_bupati')->nullable();
            $table->string('wakil_bupati_image')->nullable();
            $table->string('wali_nagari')->nullable();
            $table->string('wali_nagari_image')->nullable();
            $table->string('bamus')->nullable();
            $table->string('bamus_image')->nullable();
            $table->string('babinsa')->nullable();
            $table->string('babinsa_image')->nullable();
            $table->string('running_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_informasis');
    }
};
