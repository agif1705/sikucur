<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->unique('surat_pengantar_id', 'permohonan_surats_surat_pengantar_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('permohonan_surats', function (Blueprint $table) {
            $table->dropUnique('permohonan_surats_surat_pengantar_id_unique');
        });
    }
};

