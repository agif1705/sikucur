@php
  $absensiBy = $item['absensi_by'] ?? null;
  $status = $item['status'] ?? '-';
  $timeOnly = $item['time_only'] ?? '-';
  $isWeb = strtolower((string) $absensiBy) === 'web';
  $statusLabel = match ($status) {
    'HDDD' => 'Dinas Dalam Daerah',
    'HDLD' => 'Dinas Luar Daerah',
    'S' => 'Sakit',
    'I' => 'Izin',
    'C' => 'Cuti',
    default => $status,
  };
@endphp

@if ($absensiBy === 'Fingerprint')
  <p class="fw-bold text-muted mb-0 fs-md text-start">
    Masuk :
    @if ($item['jabatan'] === 'WaliNagari')
      <span class="fw-bold text-success text-end fst-italic">Ada di Kantor</span>
    @else
      {{ $timeOnly }}
      <span class="fw-bold {{ $item['is_late'] ? 'text-danger' : 'text-success' }} text-end fst-italic">
        {{ $item['is_late'] ? 'Terlambat' : 'Ontime' }}
        <br>{{ $absensiBy }} - {{ $statusLabel }}
      </span>
    @endif
  </p>
@elseif ($isWeb)
  <p class="fw-bold text-muted mb-0 fs-md text-start">
    <span class="fw-bold text-success text-end fst-italic">
      {{ $statusLabel }} : WhatsApp - {{ $timeOnly }}
    </span>
  </p>
@else
  <p class="fw-bold text-muted mb-0 fs-md text-start">
    @if ($item['jabatan'] === 'WaliNagari')
      <span class="fw-bold text-success text-end fst-italic">Sedang di luar Kantor</span>
    @else
      <span class="fw-bold text-danger text-end fst-italic">Tidak Masuk</span>
    @endif
  </p>
@endif
