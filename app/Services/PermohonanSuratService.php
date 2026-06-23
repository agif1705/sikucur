<?php

namespace App\Services;

use App\Models\PermohonanSurat;
use App\Models\StatusSurat;

class PermohonanSuratService
{
    /**
     * Hitung persentase progress permohonan berdasarkan urutan status.
     *
     * Status ditolak (TOLAK) dianggap "selesai diproses" sehingga bar penuh
     * (warna danger sudah ditangani oleh warna_status di view).
     */
    public function getProgressPersentase(PermohonanSurat $permohonan): int
    {
        $status = $permohonan->status;

        if (! $status) {
            return 0;
        }

        if ($status->kode_status === 'TOLAK') {
            return 100;
        }

        // Urutan maksimum dari status non-penolakan sebagai 100%.
        $maxUrutan = (int) StatusSurat::where('kode_status', '!=', 'TOLAK')->max('urutan');

        if ($maxUrutan <= 0) {
            return 0;
        }

        $persentase = ((int) $status->urutan / $maxUrutan) * 100;

        return (int) max(0, min(100, round($persentase)));
    }
}
