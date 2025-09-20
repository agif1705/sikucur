<?php

namespace App\Services;

use App\Models\PermohonanSurat;
use App\Models\JenisSurat;
use App\Models\StatusSurat;
use App\Models\TrackingSurat;
use App\Models\UploadDokumen;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PermohonanSuratService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function createPermohonan(array $data): PermohonanSurat
    {
        $jenisSurat = JenisSurat::findOrFail($data['jenis_surat_id']);

        // Set estimasi selesai berdasarkan hari kerja
        $estimasiSelesai = $this->calculateEstimasiSelesai($jenisSurat->estimasi_hari);

        $permohonan = PermohonanSurat::create(array_merge($data, [
            'status_id' => StatusSurat::where('kode_status', 'MSK')->first()->id,
            'tanggal_permohonan' => now(),
            'tanggal_estimasi_selesai' => $estimasiSelesai,
        ]));

        // Create tracking record
        $this->createTrackingRecord($permohonan, null, $permohonan->status_id);

        return $permohonan;
    }

    public function updateStatus(PermohonanSurat $permohonan, int $statusBaruId, ?string $catatan = null, ?int $petugasId = null): void
    {
        $statusLama = $permohonan->status_id;

        $permohonan->update([
            'status_id' => $statusBaruId,
            'catatan_petugas' => $catatan,
            'petugas_id' => $petugasId,
        ]);

        // Update tanggal selesai jika status selesai
        if ($this->isStatusSelesai($statusBaruId)) {
            $permohonan->update(['tanggal_selesai' => now()]);
        }

        $this->createTrackingRecord($permohonan, $statusLama, $statusBaruId, $catatan, $petugasId);
    }

    public function uploadDokumen(PermohonanSurat $permohonan, array $files): array
    {
        $uploadedFiles = [];
        $dokumenPersyaratan = $permohonan->jenisSurat->dokumenPersyaratan;

        foreach ($dokumenPersyaratan as $index => $dokumen) {
            if (isset($files[$index])) {
                $file = $files[$index];
                $path = $file->store('dokumen-surat', 'public');

                $uploadedFile = UploadDokumen::create([
                    'permohonan_id' => $permohonan->id,
                    'dokumen_persyaratan_id' => $dokumen->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);

                $uploadedFiles[] = $uploadedFile;
            }
        }

        return $uploadedFiles;
    }

    public function getProgressPersentase(PermohonanSurat $permohonan): int
    {
        $allStatuses = StatusSurat::where('kode_status', '!=', 'TLK')->orderBy('urutan')->get();
        $currentIndex = $allStatuses->search(function($status) use ($permohonan) {
            return $status->id === $permohonan->status_id;
        });

        if ($currentIndex === false) return 0;

        return (int) (($currentIndex + 1) / $allStatuses->count() * 100);
    }

    public function canDownloadSurat(PermohonanSurat $permohonan): bool
    {
        return $permohonan->status->kode_status === 'SLS' && $permohonan->suratGenerated;
    }

    private function calculateEstimasiSelesai(int $estimasiHari): Carbon
    {
        $tanggal = Carbon::now();
        $hariKerja = 0;

        while ($hariKerja < $estimasiHari) {
            $tanggal->addDay();
            // Skip weekend (Sabtu = 6, Minggu = 0)
            if (!in_array($tanggal->dayOfWeek, [0, 6])) {
                $hariKerja++;
            }
        }

        return $tanggal;
    }

    private function createTrackingRecord(PermohonanSurat $permohonan, ?int $statusLama, int $statusBaru, ?string $catatan = null, ?int $petugasId = null): void
    {
        TrackingSurat::create([
            'permohonan_id' => $permohonan->id,
            'status_lama_id' => $statusLama,
            'status_baru_id' => $statusBaru,
            'petugas_id' => $petugasId ?? auth()->id(),
            'tanggal_perubahan' => now(),
            'catatan' => $catatan,
        ]);
    }

    private function isStatusSelesai(int $statusId): bool
    {
        $status = StatusSurat::find($statusId);
        return $status && $status->kode_status === 'SLS';
    }
}