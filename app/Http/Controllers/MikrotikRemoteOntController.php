<?php

namespace App\Http\Controllers;

use App\Facades\Mikrotik;
use App\Models\MikrotikConfig;
use App\Models\MikrotikQueueDhcpTracking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class MikrotikRemoteOntController extends Controller
{
    private const LOCAL_REMOTE_ONT_URL = 'http://192.168.200.1:1709';

    private const PUBLIC_REMOTE_ONT_URL = 'http://103.101.193.110:1214';

    public function __invoke(MikrotikQueueDhcpTracking $tracking): RedirectResponse
    {
        return $this->redirectToRemoteOnt(
            tracking: $tracking,
            remoteUrl: self::LOCAL_REMOTE_ONT_URL,
            comment: 'REMOTE-Client-Ont',
            dstAddress: '192.168.200.1',
            dstPort: '1709',
        );
    }

    public function public(MikrotikQueueDhcpTracking $tracking): RedirectResponse
    {
        return $this->redirectToRemoteOnt(
            tracking: $tracking,
            remoteUrl: self::PUBLIC_REMOTE_ONT_URL,
            comment: null,
            dstAddress: null,
            dstPort: '5051',
        );
    }

    public function pppSecret(MikrotikConfig $config, string $ip, ?string $port = null): RedirectResponse
    {
        $port = $this->normalizeRemoteOntPort($port);
        $remoteHost = $this->remoteOntHost($config);

        return $this->redirectToRemoteOntIp(
            config: $config,
            ip: $ip,
            remoteUrl: "http://{$remoteHost}:{$port}",
            comment: 'REMOTE-Client-Ont',
            dstAddress: filter_var($remoteHost, FILTER_VALIDATE_IP) ? $remoteHost : null,
            dstPort: $port,
        );
    }

    public function pppSecretPublic(MikrotikConfig $config, string $ip): RedirectResponse
    {
        return $this->redirectToRemoteOntIp(
            config: $config,
            ip: $ip,
            remoteUrl: self::PUBLIC_REMOTE_ONT_URL,
            comment: null,
            dstAddress: null,
            dstPort: '5051',
        );
    }

    private function redirectToRemoteOnt(
        MikrotikQueueDhcpTracking $tracking,
        string $remoteUrl,
        ?string $comment,
        ?string $dstAddress,
        string $dstPort,
    ): RedirectResponse {
        $ip = $tracking->queue_ip ?: $tracking->lease_ip;

        abort_unless(filter_var($ip, FILTER_VALIDATE_IP), 404, 'IP tracking tidak valid.');
        abort_unless($tracking->mikrotikConfig, 404, 'Konfigurasi MikroTik tidak ditemukan.');

        try {
            Mikrotik::updateRemoteOntNat(
                config: $tracking->mikrotikConfig,
                targetIp: $ip,
                comment: $comment,
                dstPort: $dstPort,
                dstAddress: $dstAddress,
            );
        } catch (\Exception $e) {
            Log::error('Failed to prepare Remote ONT NAT before redirect', [
                'tracking_id' => $tracking->id,
                'mikrotik_config_id' => $tracking->mikrotik_config_id,
                'ip' => $ip,
                'comment' => $comment,
                'dst_address' => $dstAddress,
                'dst_port' => $dstPort,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Gagal update NAT Remote ONT: '.$e->getMessage());
        }

        return redirect()->away($remoteUrl);
    }

    private function redirectToRemoteOntIp(
        MikrotikConfig $config,
        string $ip,
        string $remoteUrl,
        ?string $comment,
        ?string $dstAddress,
        string $dstPort,
    ): RedirectResponse {
        abort_unless(filter_var($ip, FILTER_VALIDATE_IP), 404, 'IP PPP secret tidak valid.');

        try {
            Mikrotik::updateRemoteOntNat(
                config: $config,
                targetIp: $ip,
                comment: $comment,
                dstPort: $dstPort,
                dstAddress: $dstAddress,
            );
        } catch (\Exception $e) {
            Log::error('Failed to prepare Remote ONT NAT from PPP secret before redirect', [
                'mikrotik_config_id' => $config->id,
                'ip' => $ip,
                'comment' => $comment,
                'dst_address' => $dstAddress,
                'dst_port' => $dstPort,
                'error' => $e->getMessage(),
            ]);

            abort(500, 'Gagal update NAT Remote ONT: '.$e->getMessage());
        }

        return redirect()->away($remoteUrl);
    }

    private function normalizeRemoteOntPort(?string $port): string
    {
        $port = (int) ($port ?: 1710);

        if ($port < 1 || $port > 65535) {
            abort(404, 'Port Remote ONT tidak valid.');
        }

        return (string) $port;
    }

    private function remoteOntHost(MikrotikConfig $config): string
    {
        $url = filled($config->rest_url) ? $config->rest_url : $config->host;

        return parse_url($url, PHP_URL_HOST) ?: str($url)->replace(['http://', 'https://'], '')->before('/')->toString();
    }
}
