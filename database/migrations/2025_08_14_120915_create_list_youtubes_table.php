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
        Schema::create('list_youtubes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nagari_id')->constrained('nagaris')->cascadeOnDelete();
            $table->string('url');
            $table->string('id_youtube');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_youtubes');
    }
};
