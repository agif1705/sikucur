<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->foreignId('surat_pengantar_id')
                ->nullable()
                ->after('penduduk_id')
                ->constrained('surat_pengantars')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->dropForeign(['surat_pengantar_id']);
            $table->dropColumn('surat_pengantar_id');
        });
    }
};
