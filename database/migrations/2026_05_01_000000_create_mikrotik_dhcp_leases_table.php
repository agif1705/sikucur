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
        Schema::create('mikrotik_dhcp_leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mikrotik_config_id')->constrained('mikrotik_configs')->onDelete('cascade');
            $table->string('ret_id')->nullable();
            $table->string('mac_address');
            $table->string('address')->nullable();
            $table->string('server')->nullable();
            $table->string('host_name')->nullable();
            $table->string('client_id')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('dynamic')->default(false);
            $table->boolean('disabled')->default(false);
            $table->timestamps();

            $table->unique(['mikrotik_config_id', 'mac_address']);
            $table->unique(['mikrotik_config_id', 'ret_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mikrotik_dhcp_leases');
    }
};
