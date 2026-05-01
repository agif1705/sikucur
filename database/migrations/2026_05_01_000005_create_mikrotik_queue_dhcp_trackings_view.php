<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $view = $this->quoteIdentifier(DB::getTablePrefix().'mikrotik_queue_dhcp_trackings');
        $leases = $this->quoteIdentifier(DB::getTablePrefix().'mikrotik_dhcp_leases');
        $queues = $this->quoteIdentifier(DB::getTablePrefix().'mikrotik_queues');

        DB::statement("DROP VIEW IF EXISTS {$view}");

        DB::statement(<<<SQL
            CREATE VIEW {$view} AS
            WITH leases AS (
                SELECT
                    id,
                    mikrotik_config_id,
                    comment AS dhcp_name,
                    COALESCE(address, active_address) AS lease_ip,
                    address AS lease_address,
                    active_address AS lease_active_address
                FROM {$leases}
            ),
            queues AS (
                SELECT
                    id,
                    mikrotik_config_id,
                    name AS queue_name,
                    comment AS queue_comment,
                    target AS queue_target,
                    substring(target from '([0-9]{1,3}(?:\.[0-9]{1,3}){3})') AS queue_ip
                FROM {$queues}
            ),
            matches AS (
                SELECT
                    COALESCE(leases.mikrotik_config_id, queues.mikrotik_config_id) AS mikrotik_config_id,
                    leases.id AS dhcp_lease_id,
                    queues.id AS queue_id,
                    leases.dhcp_name,
                    leases.lease_ip,
                    leases.lease_address,
                    leases.lease_active_address,
                    queues.queue_ip,
                    queues.queue_name,
                    queues.queue_comment,
                    queues.queue_target
                FROM leases
                FULL OUTER JOIN queues
                    ON leases.mikrotik_config_id = queues.mikrotik_config_id
                    AND leases.lease_ip IS NOT NULL
                    AND queues.queue_ip IS NOT NULL
                    AND leases.lease_ip = queues.queue_ip
            )
            SELECT
                row_number() OVER (
                    ORDER BY
                        mikrotik_config_id,
                        COALESCE(lease_ip, queue_ip, ''),
                        COALESCE(dhcp_name, queue_comment, queue_name, '')
                ) AS id,
                mikrotik_config_id,
                dhcp_lease_id,
                queue_id,
                dhcp_name,
                lease_ip,
                lease_address,
                lease_active_address,
                queue_ip,
                queue_name,
                queue_comment,
                queue_target,
                CASE
                    WHEN queue_id IS NOT NULL AND (queue_ip IS NULL OR btrim(queue_ip) = '') THEN 'Queue Belum Ada IP'
                    WHEN queue_id IS NULL THEN 'Belum Ada Queue'
                    WHEN dhcp_lease_id IS NULL THEN 'Belum Ada DHCP Lease'
                    WHEN queue_comment IS NULL OR btrim(queue_comment) = '' THEN 'Queue Belum Ada Comment'
                    WHEN dhcp_name IS NULL OR btrim(dhcp_name) = '' THEN 'DHCP Lease Belum Ada Nama'
                    ELSE 'Lengkap'
                END AS tracking_status
            FROM matches
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $view = $this->quoteIdentifier(DB::getTablePrefix().'mikrotik_queue_dhcp_trackings');

        DB::statement("DROP VIEW IF EXISTS {$view}");
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '"'.str_replace('"', '""', $identifier).'"';
    }
};
