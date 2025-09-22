<?php

namespace App\Livewire\Surat;

use App\Models\PermohonanSurat;
use App\Services\PermohonanSuratService;
use Livewire\Component;

class SuratTracking extends Component
{
    public $nomorPermohonan = '';
    public $permohonan = null;
    public $showDetail = false;

    protected $rules = [
        'nomorPermohonan' => 'required|string',
    ];

    public function search()
    {
        $this->validate();

        $this->permohonan = PermohonanSurat::with([
            'jenisSurat',
            'status',
            'nagari',
            'uploadDokumen.dokumenPersyaratan',
            'trackingSurat.statusBaru',
            'trackingSurat.petugas',
            'suratGenerated'
        ])
        ->where('nomor_permohonan', $this->nomorPermohonan)
        ->first();

        if ($this->permohonan) {
            $this->showDetail = true;
        } else {
            session()->flash('error', 'Nomor permohonan tidak ditemukan!');
        }
    }

    public function reset()
    {
        $this->nomorPermohonan = '';
        $this->permohonan = null;
        $this->showDetail = false;
    }

    public function getProgressAttribute()
    {
        if (!$this->permohonan) return 0;

        $service = new PermohonanSuratService();
        return $service->getProgressPersentase($this->permohonan);
    }

    public function render()
    {
        return view('livewire.surat-tracking');
    }
}