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
        Schema::table('jenis_surat', function (Blueprint $table) {
            if (! Schema::hasColumn('jenis_surat', 'estimasi_hari')) {
                $table->unsignedInteger('estimasi_hari')->nullable()->after('mandiri');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_surat', function (Blueprint $table) {
            if (Schema::hasColumn('jenis_surat', 'estimasi_hari')) {
                $table->dropColumn('estimasi_hari');
            }
        });
    }
};
