<!DOCTYPE html>
<html>

<head>
  <style>
    @page {
      size: A4 landscape;
      margin: 0.1cm;
    }

    body {
      font-family: Arial, sans-serif;
      font-size: 8pt;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      border: 1px solid #ddd;
      padding: 3px;
      text-align: center;
    }

    th {
      background-color: #f2f2f2;
    }

    .holiday {
      background-color: #ce3232;
    }

    .weekend {
      background-color: #f9f9f9;
    }

    .header-date {
      writing-mode: vertical-rl;
      transform: rotate(180deg);
    }

    .time-entry {
      height: 20px;
    }

    .masuk {
      background-color: #16c472;
    }

    .pulang {
      background-color: #d0ec6c;
    }

    .empty {
      background-color: #ffcccc;
    }

    .text-center {
      text-align: center;
    }

    .table-libur {
      width: 25%;
      border-collapse: collapse;
      margin: 10px;
    }

    .horizontal-holidays {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      margin: 20px 0;
    }

    .holiday-card {
      border: 1px solid #ddd;
      padding: 10px 15px;
      border-radius: 5px;
      background: #e45a5a;
      min-width: 200px;
    }

    .status-sakit {
      background-color: #ff9999 !important;
      color: #000;
    }

    .status-izin {
      background-color: #ffcc99 !important;
      color: #000;
    }

    .status-cuti {
      background-color: #cc99ff !important;
      color: #000;
    }

    .status-alpha {
      background-color: #ffcccc !important;
      color: #000;
    }

    .status-hdld {
      background-color: #99ff99 !important;
      color: #000;
    }

    .status-hddd {
      background-color: #99ccff !important;
      color: #000;
    }
  </style>
</head>

<body>
  <h1 class="text-center">Nagari Sikucur - Kehadiran {{ $monthName }}</h1>
  <h5>Laporan ini di cetak tanggal {{ now() }}</h5>
  <table>
    <thead>
      <tr>
        <th>Nama Pegawai</th>
        @foreach ($datesInMonth as $date)
          @php
            $dateObj = Carbon\Carbon::parse($date);
            $isHoliday = in_array($date, $holidays);
          @endphp
          <th colspan="1" class="@if ($isHoliday) holiday @endif">
            {{ $dateObj->format('d') }}<br>
            {{ $dateObj->translatedFormat('D') }}
          </th>
        @endforeach
        <th>Work<br>day</th>
        <th>Ttl<br>Msk</th>
        <th>Ter<br>lambat</th>
        <th>Tidak<br>Hadir</th>
        <th>Keha<br>diran</th>
        <th>On<br>time</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($attendanceData as $data)
        <tr>
          <td style="text-align: left">{{ $data['user']->name }}</td>

          @foreach ($datesInMonth as $date)
            @php
              $attendance = $data['attendances'][$date];
              $isHoliday = in_array($date, $holidays);
              $statusClass = '';

              if (isset($attendance['status_absensi'])) {
                  switch ($attendance['status_absensi']) {
                      case 'S':
                          $statusClass = 'status-sakit';
                          break;
                      case 'I':
                          $statusClass = 'status-izin';
                          break;
                      case 'C':
                          $statusClass = 'status-cuti';
                          break;
                      case 'HDLD':
                          $statusClass = 'status-hdld';
                          break;
                      case 'HDDD':
                          $statusClass = 'status-hddd';
                          break;
                      case 'A':
                          $statusClass = 'status-alpha';
                          break;
                  }
              }
            @endphp
            <td class="@if ($isHoliday) holiday @else {{ $statusClass }} @endif">
              <div class="time-entry masuk @if ($attendance['masuk'] == 'A' && !$isHoliday) empty @endif">
                <span style="@if ($attendance['is_late']) color:red @endif">{{ $attendance['masuk'] }}</span>
              </div>
              <div class="time-entry pulang @if ($attendance['pulang'] == '-' && !$isHoliday) empty @endif">
                {{ $attendance['pulang'] ?? 'A' }}
              </div>
            </td>
          @endforeach
          <td>{{ $attendance['total_hari_kerja'] }}</td>
          <td>{{ $attendance['total_masuk'] }}</td>
          <td>{{ $data['total_late'] }}</td>
          <td>{{ $data['total_tidak_hadir'] }}</td>
          <td>
            @if ($attendance['total_hari_kerja'] > 0)
              {{ round(($attendance['total_masuk'] / $attendance['total_hari_kerja']) * 100) }}%
            @else
              0%
            @endif
          </td>

          <td>
            @if ($attendance['total_masuk'] > 0)
              {{ round((($attendance['total_masuk'] - $data['total_late']) / $attendance['total_masuk']) * 100) }}%
            @else
              0%
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  {{-- <table class="table-libur">
        <thead>
            <tr>
                <th>Hari Libur</th>
                <th>Tanggal Libur</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($holidays as $item => $value)
                <tr>
                    <td>{{ $item }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </tbody>
    </table> --}}
  @if (count($detailKeterangan) > 0)
    <!-- Page Break untuk Halaman 2 -->
    <div style="page-break-before: always;"></div>

    <h1 class="text-center">Detail Keterangan Absensi - {{ $monthName }}</h1>
    <h5>Laporan ini di cetak tanggal {{ now() }}</h5>

    <table style="margin-top: 20px;">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Pegawai</th>
          <th>Tanggal</th>
          <th>Status</th>
          <th>Keterangan Status</th>
          <th>Alasan/Keterangan</th>
          <th>Jam Masuk</th>
          <th>Jam Pulang</th>
          <th>Terlambat</th>
          <th>Sumber Data</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($detailKeterangan as $index => $detail)
          <tr>
            <td>{{ $index + 1 }}</td>
            <td style="text-align: left;">{{ $detail['user_name'] }}</td>
            <td>{{ Carbon\Carbon::parse($detail['date'])->translatedFormat('d F Y') }}</td>
            <td
              style="text-align: center; font-weight: bold;
          @if ($detail['status_absensi'] == 'S') background-color: #ff9999;
          @elseif($detail['status_absensi'] == 'I') background-color: #ffcc99;
          @elseif($detail['status_absensi'] == 'C') background-color: #cc99ff;
          @elseif($detail['status_absensi'] == 'HDLD') background-color: #99ff99;
          @elseif($detail['status_absensi'] == 'HDDD') background-color: #99ccff; @endif
        ">
              {{ $detail['status_absensi'] }}</td>
            <td style="text-align: left;">
              @switch($detail['status_absensi'])
                @case('HDLD')
                  Hadir Dinas Luar Daerah
                @break

                @case('HDDD')
                  Hadir Dinas Dalam Daerah
                @break

                @case('S')
                  Sakit
                @break

                @case('C')
                  Cuti
                @break

                @case('I')
                  Izin
                @break

                @default
                  -
              @endswitch
            </td>
            <td style="text-align: left; font-size: 7pt; max-width: 100px;">
              @if (isset($detail['alasan']) && $detail['alasan'])
                {{ $detail['alasan'] }}
                @if (isset($detail['id_resource']) && str_starts_with($detail['id_resource'], 'web-'))
                  <br><small style="color: #666; font-style: italic;">(ID: {{ $detail['id_resource'] }})</small>
                @endif
              @else
                <span style="color: #999;">-</span>
              @endif
            </td>
            <td>{{ $detail['time_in'] ? Carbon\Carbon::parse($detail['time_in'])->format('H:i') : '-' }}</td>
            <td>{{ $detail['time_out'] ? Carbon\Carbon::parse($detail['time_out'])->format('H:i') : '-' }}</td>
            <td style="color: @if ($detail['is_late']) red @else green @endif; text-align: center;">
              @if ($detail['is_late'])
                Ya
              @else
                Tidak
              @endif
            </td>
            <td>{{ $detail['resource'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
      <p><strong>Total {{ count($detailKeterangan) }} record dengan keterangan khusus</strong></p>
      <p><strong>Keterangan Status:</strong></p>
      <ul style="margin: 5px 0; padding-left: 20px;">
        <li><strong>HDLD:</strong> Hadir Dinas Luar Daerah - Pegawai bertugas di luar daerah</li>
        <li><strong>HDDD:</strong> Hadir Dinas Dalam Daerah - Pegawai bertugas dalam daerah</li>
        <li><strong>S:</strong> Sakit - Pegawai tidak hadir karena sakit</li>
        <li><strong>C:</strong> Cuti - Pegawai mengambil cuti</li>
        <li><strong>I:</strong> Izin - Pegawai tidak hadir dengan izin</li>
      </ul>
    </div>

    @if (count($holidays) > 0)
      <!-- Tabel Hari Libur -->
      <div style="margin-top: 30px;">
        <h3 style="margin-bottom: 10px; color: #ce3232;">Daftar Hari Libur Nasional - {{ $monthName }}</h3>

        <table style="margin-top: 10px; width: 80%;">
          <thead>
            <tr style="background-color: #ce3232; color: white;">
              <th style="background-color: #ce3232; color: white;">No</th>
              <th style="background-color: #ce3232; color: white;">Nama Hari Libur</th>
              <th style="background-color: #ce3232; color: white;">Tanggal</th>
              <th style="background-color: #ce3232; color: white;">Hari</th>
              <th style="background-color: #ce3232; color: white;">Status</th>
            </tr>
          </thead>
          <tbody>
            @php $no = 1; @endphp
            @foreach ($holidays as $holidayName => $holidayDate)
              <tr style="@if ($loop->iteration % 2 == 0) background-color: #ffe6e6; @endif">
                <td>{{ $no++ }}</td>
                <td style="text-align: left; padding-left: 5px;">{{ $holidayName }}</td>
                <td>{{ Carbon\Carbon::parse($holidayDate)->translatedFormat('d F Y') }}</td>
                <td>{{ Carbon\Carbon::parse($holidayDate)->translatedFormat('l') }}</td>
                <td style="background-color: #ff9999; font-weight: bold; color: #800000;">LIBUR</td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <!-- Legend Keterangan -->
        <div style="margin: 10px 0; font-size: 10px;">
          <strong>Keterangan:</strong>
          <span style="color: green;">H = Hadir</span> |
          <span style="color: red;">A = Alpha</span> |
          <span style="color: blue;">S = Sakit</span> |
          <span style="color: orange;">I = Izin</span> |
          <span style="color: purple;">C = Cuti</span> |
          <span style="color: darkgreen;">HDLD = Hadir Dinas Luar Daerah</span> |
          <span style="color: darkblue;">HDDD = Hadir Dinas Dalam Daerah</span> |
          <span style="background-color: orange; padding: 2px;">L = Libur</span>
        </div>
        <div style="margin-top: 15px; font-size: 9pt; color: #666;">
          <p><strong>Catatan Hari Libur:</strong></p>
          <ul style="margin: 5px 0; padding-left: 20px; font-size: 8pt;">
            <li>Total <strong>{{ count($holidays) }} hari libur nasional</strong> pada bulan {{ $monthName }}</li>
            <li>Hari libur ditandai dengan background <span
                style="background-color: #ce3232; color: white; padding: 2px 4px;">merah</span> pada kalender absensi
            </li>
            <li>Pegawai <strong>tidak wajib hadir</strong> pada tanggal-tanggal tersebut</li>
            <li>Status "L" pada tabel absensi menunjukkan hari libur nasional</li>
          </ul>
        </div>
      </div>
    @else
      <!-- Pesan jika tidak ada hari libur -->
      <div
        style="margin-top: 30px; padding: 15px; background-color: #f0f8ff; border: 1px solid #b0d4f1; border-radius: 5px;">
        <h4 style="margin: 0; color: #2c5aa0;">ℹ️ Informasi Hari Libur</h4>
        <p style="margin: 5px 0; font-size: 9pt; color: #555;">
          Tidak ada hari libur nasional yang tercatat pada bulan {{ $monthName }}.
        </p>
      </div>
    @endif
  @endif

  <footer>
    <h5>Laporan ini di cetak tanggal {{ now() }}</h5>
    <h6>Dicetak ole Pegawai: {{ auth()->user()->name ?? 'Anonim' }}</h6>
  </footer>
</body>

</html>
