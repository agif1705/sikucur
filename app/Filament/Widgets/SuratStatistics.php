<?php

namespace App\Filament\Widgets;

use App\Models\PermohonanSurat;
use App\Models\StatusSurat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class SuratStatistics extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        return [
            Stat::make('Total Permohonan Hari Ini', PermohonanSurat::whereDate('tanggal_permohonan', $today)->count())
                ->description('Permohonan masuk hari ini')
                ->descriptionIcon('heroicon-m-document-plus')
                ->color('primary'),

            Stat::make('Total Permohonan Bulan Ini', PermohonanSurat::where('tanggal_permohonan', '>=', $thisMonth)->count())
                ->description('Permohonan bulan ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success'),

            Stat::make('Menunggu Verifikasi', PermohonanSurat::whereHas('status', function($query) {
                    $query->where('kode_status', 'MSK');
                })->count())
                ->description('Perlu ditindaklanjuti')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Surat Selesai', PermohonanSurat::whereHas('status', function($query) {
                    $query->where('kode_status', 'SLS');
                })->count())
                ->description('Total surat selesai')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
        ];
    }
}
