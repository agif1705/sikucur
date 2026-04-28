<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_pengantars', function (Blueprint $table) {
            $table->text('pemohon_alamat_domisili')->nullable()->after('pemohon_alamat');
        });

        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->text('pemohon_alamat_domisili')->nullable()->after('pemohon_alamat');
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->dropColumn('pemohon_alamat_domisili');
        });

        Schema::table('surat_pengantars', function (Blueprint $table) {
            $table->dropColumn('pemohon_alamat_domisili');
        });
    }
};
