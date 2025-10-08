<?php

namespace App\Filament\Widgets;

use App\Models\WhatsAppBroadcast;
use App\Services\WhatsAppBroadcastService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BroadcastStatsWidget extends BaseWidget
{
 protected function getStats(): array
 {
  $stats = app(WhatsAppBroadcastService::class)->getBroadcastStats();

  return [
   Stat::make('Total Broadcast', $stats['total_broadcasts'])
    ->description('Total broadcast yang pernah dibuat')
    ->descriptionIcon('heroicon-m-megaphone')
    ->color('primary'),

   Stat::make('Broadcast Selesai', $stats['completed_broadcasts'])
    ->description('Broadcast yang berhasil dikirim')
    ->descriptionIcon('heroicon-m-check-circle')
    ->color('success'),

   Stat::make('Total Penerima', number_format($stats['total_recipients']))
    ->description('Total penerima dari semua broadcast')
    ->descriptionIcon('heroicon-m-users')
    ->color('info'),

   Stat::make('Tingkat Keberhasilan', round($stats['success_rate'], 1) . '%')
    ->description('Persentase keberhasilan pengiriman')
    ->descriptionIcon('heroicon-m-chart-bar')
    ->color(match (true) {
     $stats['success_rate'] >= 90 => 'success',
     $stats['success_rate'] >= 70 => 'warning',
     $stats['success_rate'] >= 50 => 'info',
     default => 'danger',
    }),
  ];
 }
}
