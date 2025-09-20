{{-- filepath: resources/views/livewire/surat-tracking.blade.php --}}
<div class="container my-5">
  @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  @if (!$showDetail)
    {{-- Search Form --}}
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card shadow">
          <div class="card-header bg-info text-white text-center">
            <h4 class="mb-0">
              <i class="fas fa-search"></i>
              Lacak Status Permohonan Surat
            </h4>
          </div>
          <div class="card-body">
            <form wire:submit.prevent="search">
              <div class="mb-3">
                <label for="nomorPermohonan" class="form-label">Nomor Permohonan</label>
                <input type="text" class="form-control @error('nomorPermohonan') is-invalid @enderror"
                  id="nomorPermohonan" wire:model="nomorPermohonan" placeholder="Contoh: 001/SURAT/001/09/2025"
                  autocomplete="off">
                @error('nomorPermohonan')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="d-grid">
                <button type="submit" class="btn btn-info" wire:loading.attr="disabled">
                  <span wire:loading.remove>
                    <i class="fas fa-search"></i> Cari Permohonan
                  </span>
                  <span wire:loading>
                    <i class="fas fa-spinner fa-spin"></i> Mencari...
                  </span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  @else
    {{-- Detail Tracking --}}
    <div class="row">
      <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h3>Detail Permohonan Surat</h3>
          <button wire:click="reset" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Cari Lagi
          </button>
        </div>
      </div>
    </div>

    @if ($permohonan)
      {{-- Progress Bar --}}
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">Progress Permohonan</h6>
              <div class="progress mb-2" style="height: 25px;">
                <div
                  class="progress-bar progress-bar-striped progress-bar-animated bg-{{ $permohonan->status->warna_status }}"
                  role="progressbar" style="width: {{ $this->progress }}%" aria-valuenow="{{ $this->progress }}"
                  aria-valuemin="0" aria-valuemax="100">
                  {{ $this->progress }}%
                </div>
              </div>
              <div class="d-flex justify-content-between">
                <small class="text-muted">Status: {{ $permohonan->status->nama_status }}</small>
                @if ($permohonan->tanggal_estimasi_selesai)
                  <small class="text-muted">
                    Estimasi Selesai: {{ $permohonan->tanggal_estimasi_selesai->format('d M Y') }}
                  </small>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        {{-- Informasi Permohonan --}}
        <div class="col-lg-6 mb-4">
          <div class="card h-100">
            <div class="card-header bg-primary text-white">
              <h6 class="mb-0">
                <i class="fas fa-info-circle"></i>
                Informasi Permohonan
              </h6>
            </div>
            <div class="card-body">
              <table class="table table-borderless table-sm">
                <tr>
                  <td width="40%"><strong>Nomor Permohonan</strong></td>
                  <td>: {{ $permohonan->nomor_permohonan }}</td>
                </tr>
                <tr>
                  <td><strong>Jenis Surat</strong></td>
                  <td>: {{ $permohonan->jenisSurat->nama_jenis }}</td>
                </tr>
                <tr>
                  <td><strong>Nama Pemohon</strong></td>
                  <td>: {{ $permohonan->pemohon_nama }}</td>
                </tr>
                <tr>
                  <td><strong>NIK</strong></td>
                  <td>: {{ $permohonan->pemohon_nik }}</td>
                </tr>
                <tr>
                  <td><strong>Tanggal Permohonan</strong></td>
                  <td>: {{ $permohonan->tanggal_permohonan->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                  <td><strong>Keperluan</strong></td>
                  <td>: {{ $permohonan->keperluan }}</td>
                </tr>
                @if ($permohonan->catatan_petugas)
                  <tr>
                    <td><strong>Catatan Petugas</strong></td>
                    <td>: {{ $permohonan->catatan_petugas }}</td>
                  </tr>
                @endif
              </table>

              @if ($permohonan->status->kode_status === 'SLS' && $permohonan->suratGenerated)
                <div class="mt-3">
                  <a href="{{ route('surat.download', $permohonan->nomor_permohonan) }}"
                    class="btn btn-success btn-sm">
                    <i class="fas fa-download"></i> Download Surat
                  </a>
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Timeline Status --}}
        <div class="col-lg-6">
          <div class="card h-100">
            <div class="card-header bg-success text-white">
              <h6 class="mb-0">
                <i class="fas fa-history"></i>
                Riwayat Status
              </h6>
            </div>
            <div class="card-body">
              <div class="timeline">
                @foreach ($permohonan->trackingSurat->sortByDesc('tanggal_perubahan') as $tracking)
                  <div class="timeline-item">
                    <div class="timeline-marker bg-{{ $tracking->statusBaru->warna_status }}"></div>
                    <div class="timeline-content">
                      <h6 class="timeline-title">{{ $tracking->statusBaru->nama_status }}</h6>
                      <p class="timeline-text">
                        {{ $tracking->statusBaru->deskripsi }}
                        @if ($tracking->catatan)
                          <br><small class="text-muted">Catatan: {{ $tracking->catatan }}</small>
                        @endif
                      </p>
                      <small class="timeline-date text-muted">
                        <i class="fas fa-clock"></i>
                        {{ $tracking->tanggal_perubahan->format('d M Y H:i') }}
                        @if ($tracking->petugas)
                          | {{ $tracking->petugas->name }}
                        @endif
                      </small>
                    </div>
                  </div>
                @endforeach
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- Dokumen yang Diupload --}}
      @if ($permohonan->uploadDokumen->count() > 0)
        <div class="row mt-4">
          <div class="col-12">
            <div class="card">
              <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                  <i class="fas fa-paperclip"></i>
                  Dokumen yang Diupload
                </h6>
              </div>
              <div class="card-body">
                <div class="row">
                  @foreach ($permohonan->uploadDokumen as $upload)
                    <div class="col-md-6 col-lg-4 mb-3">
                      <div class="card border">
                        <div class="card-body p-3">
                          <h6 class="card-title text-truncate">
                            {{ $upload->dokumenPersyaratan->nama_dokumen }}
                          </h6>
                          <p class="card-text">
                            <small class="text-muted">
                              {{ $upload->file_name }}<br>
                              {{ $upload->formatted_file_size }}
                            </small>
                          </p>
                          <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $upload->status_color }}">
                              {{ $upload->status_verifikasi }}
                            </span>
                            @if ($upload->is_image)
                              <button class="btn btn-sm btn-outline-primary"
                                onclick="previewImage('{{ $upload->file_url }}')">
                                <i class="fas fa-eye"></i>
                              </button>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
      @endif
    @endif
  @endif
</div>

{{-- CSS untuk Timeline --}}
<style>
  .timeline {
    position: relative;
    padding-left: 30px;
  }

  .timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
  }

  .timeline-item {
    position: relative;
    margin-bottom: 30px;
  }

  .timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
  }

  .timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #007bff;
  }

  .timeline-title {
    margin-bottom: 5px;
    font-size: 14px;
    font-weight: 600;
  }

  .timeline-text {
    margin-bottom: 8px;
    font-size: 13px;
    line-height: 1.4;
  }

  .timeline-date {
    font-size: 11px;
  }
</style>
