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
        Schema::create('mikrotik_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mikrotik_config_id')->constrained('mikrotik_configs')->onDelete('cascade');
            $table->string('ret_id')->nullable();
            $table->string('name');
            $table->text('target')->nullable();
            $table->text('dst')->nullable();
            $table->string('parent')->nullable();
            $table->text('packet_marks')->nullable();
            $table->string('priority')->nullable();
            $table->string('queue_type')->nullable();
            $table->string('limit_at')->nullable();
            $table->string('max_limit')->nullable();
            $table->string('burst_limit')->nullable();
            $table->string('burst_threshold')->nullable();
            $table->string('burst_time')->nullable();
            $table->string('rate')->nullable();
            $table->string('bytes')->nullable();
            $table->string('total_bytes')->nullable();
            $table->string('packets')->nullable();
            $table->string('total_packets')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('dynamic')->default(false);
            $table->boolean('disabled')->default(false);
            $table->boolean('invalid')->default(false);
            $table->timestamps();

            $table->unique(['mikrotik_config_id', 'ret_id']);
            $table->index(['mikrotik_config_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_queues');
    }
};
