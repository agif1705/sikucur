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
        Schema::create('whats_app_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('sender_id');
            $table->foreign('sender_id')->references('id')->on('users');
            $table->string('target_type')->default('all');
            $table->json('target_ids')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('total_sent')->default(0);
            $table->integer('total_failed')->default(0);
            $table->string('status')->default('draft');
            $table->string('attachment_path')->nullable()->after('message');
            $table->string('attachment_type')->nullable()->after('attachment_path');
            $table->string('attachment_name')->nullable()->after('attachment_type');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whats_app_broadcasts');
    }
};
