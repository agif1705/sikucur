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
        Schema::table('mikrotik_dhcp_leases', function (Blueprint $table) {
            $table->string('active_address')->nullable()->after('address');
            $table->string('status')->nullable()->after('client_id');
            $table->string('last_seen')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mikrotik_dhcp_leases', function (Blueprint $table) {
            $table->dropColumn(['active_address', 'status', 'last_seen']);
        });
    }
};
