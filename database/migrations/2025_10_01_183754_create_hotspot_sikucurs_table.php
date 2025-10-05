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
        Schema::create('hotspot_sikucurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penduduk_id')->constrained('penduduks')->onDelete('cascade');
            $table->foreignId('mikrotik_config_id')->constrained('mikrotik_configs')->onDelete('cascade');
            $table->string('ret_id')->unique();
            $table->string('phone_mikrotik')->unique();
            $table->string('mikrotik_id')->unique();
            $table->boolean('status')->default(true);
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotspot_sikucurs');
    }
};
