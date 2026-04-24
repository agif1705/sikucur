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
        Schema::table('tracking_surat', function (Blueprint $table) {
            $table->dropForeign(['permohonan_id']);

            $table->foreign('permohonan_id')
                ->references('id')
                ->on('permohonan_surats')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracking_surat', function (Blueprint $table) {
            $table->dropForeign(['permohonan_id']);

            $table->foreign('permohonan_id')
                ->references('id')
                ->on('permohonan_surats');
        });
    }
};
