<?php

namespace App\Http\Controllers;

use App\Facades\Mikrotik;
use App\Models\MikrotikQueueDhcpTracking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class MikrotikRemoteOntController extends Controller
{
    private const REMOTE_ONT_URL = 'http://192.168.200.1:1709';

    public function __invoke(MikrotikQueueDhcpTracking $tracking): RedirectResponse
    {
        $ip = $tracking->queue_ip ?: $tracking->lease_ip;

        abort_unless(filter_var($ip, FILTER_VALIDATE_IP), 404, 'IP tracking tidak valid.');
        abort_unless($tracking->mikrotikConfig, 404, 'Konfigurasi MikroTik tidak ditemukan.');

        try {
            Mikrotik::updateRemoteOntNat($tracking->mikrotikConfig, $ip);
        } catch (\Exception $e) {
            Log::error('Failed to prepare Remote ONT NAT before redirect', [
                'tracking_id' => $tracking->id,
                'mikrotik_config_id' => $tracking->mikrotik_config_id,
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Gagal update NAT Remote ONT: '.$e->getMessage());
        }

        return redirect()->away(self::REMOTE_ONT_URL);
    }
}
