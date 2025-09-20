<?php

namespace App\Filament\Widgets;

use App\Models\PermohonanSurat;
use App\Models\StatusSurat;
use Filament\Widgets\ChartWidget;

class SuratProgressChart extends ChartWidget
{
    protected static ?string $heading = 'Status Permohonan Surat';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statusData = StatusSurat::withCount('permohonanSurat')
            ->orderBy('urutan')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Permohonan',
                    'data' => $statusData->pluck('permohonan_surat_count')->toArray(),
                    'backgroundColor' => [
                        '#3b82f6', // primary
                        '#f59e0b', // warning
                        '#06b6d4', // info
                        '#6b7280', // secondary
                        '#10b981', // success
                        '#ef4444', // danger
                    ],
                ],
            ],
            'labels' => $statusData->pluck('nama_status')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}