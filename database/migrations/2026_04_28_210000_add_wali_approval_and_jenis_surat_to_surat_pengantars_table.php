<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_pengantars', function (Blueprint $table) {
            $table->foreignId('jenis_surat_id')
                ->nullable()
                ->after('wali_korong_id')
                ->constrained('jenis_surat')
                ->nullOnDelete();

            $table->timestamp('wali_response_at')
                ->nullable()
                ->after('tanggal_pengantar');
        });
    }

    public function down(): void
    {
        Schema::table('surat_pengantars', function (Blueprint $table) {
            $table->dropConstrainedForeignId('jenis_surat_id');
            $table->dropColumn('wali_response_at');
        });
    }
};
