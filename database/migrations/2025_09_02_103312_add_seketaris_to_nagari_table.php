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
          Schema::table('nagaris', function (Blueprint $table) {
            $table->foreignId('seketaris_id')
            ->nullable()
                ->constrained('users') // relasi ke tabel users
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('nagaris', function (Blueprint $table) {
            $table->dropForeign(['seketaris_id']);
            $table->dropColumn('seketaris_id');
        });
    }
};
