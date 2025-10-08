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
        Schema::create('whats_app_broadcast_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('whats_app_broadcast_id');
            $table->unsignedBigInteger('user_id');
            $table->foreign('whats_app_broadcast_id')->references('id')->on('whats_app_broadcasts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('phone');
            $table->boolean('status')->default(false); // true = sent, false = failed
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->unsignedBigInteger('penduduk_id')->nullable()->after('user_id');
            $table->string('recipient_type')->default('user')->after('penduduk_id');
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('penduduk_id')->references('id')->on('penduduks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_broadcast_logs');
    }
};
