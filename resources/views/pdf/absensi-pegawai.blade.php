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
      color: white;
    }

    .holiday-api {
      background-color: #ff8c00;
      color: white;
    }

    .future-date {
      background-color: #e0e0e0;
      color: #666;
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
  </style>
</head>

<body>
  <h1 class="text-center">Nagari Sikucur - Kehadiran {{ $monthName }}</h1>
  <h5>Laporan ini dicetak tanggal: {{ now()->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }} WIB</h5>

  <table>
    <thead>
      <tr>
        <th>Nama Pegawai</th>
        @foreach ($datesInMonth as $date)
          @php
            $dateObj = Carbon\Carbon::parse($date);
            $isHolidayApi = isset($holidays[$date]);
            $isFutureDate = $date > now()->toDateString();
          @endphp
          <th colspan="1"
            class="@if ($isHolidayApi) holiday-api @elseif($isFutureDate) future-date @endif"
            @if ($isHolidayApi) title="{{ $holidays[$date]['name'] }}" @endif>
            {{ $dateObj->format('d') }}<br>
            {{ $dateObj->translatedFormat('D') }}
            @if ($isHolidayApi)
              <br><small>{{ $holidays[$date]['name'] }}</small>
            @endif
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
              $isHolidayApi = isset($holidays[$date]);
              $isFutureDate = isset($attendance['is_future']) && $attendance['is_future'];
            @endphp
            <td
              class="@if ($isHolidayApi) holiday-api @elseif($isFutureDate) future-date @endif"
              @if ($isHolidayApi) title="{{ $holidays[$date]['name'] }}" @endif>
              <div class="time-entry masuk">
                <span style="@if (isset($attendance['is_late']) && $attendance['is_late']) color:red @endif">
                  {{ $attendance['masuk'] }}
                </span>
              </div>
              <div class="time-entry pulang">
                {{ $attendance['pulang'] ?? '-' }}
              </div>
            </td>
          @endforeach
          <td>{{ $data['stats']['total_hari_kerja'] }}</td>
          <td>{{ $data['stats']['total_present'] }}</td>
          <td>{{ $data['stats']['total_late'] }}</td>
          <td>{{ $data['stats']['total_tidak_hadir'] }}</td>
          <td>
            @if ($data['stats']['total_hari_kerja'] > 0)
              {{ round(($data['stats']['total_present'] / $data['stats']['total_hari_kerja']) * 100) }}%
            @else
              0%
            @endif
          </td>

          <td>
            @if ($data['stats']['total_present'] > 0)
              {{ round((($data['stats']['total_present'] - $data['stats']['total_late']) / $data['stats']['total_present']) * 100) }}%
            @else
              0%
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <table border="0" cellspacing="10" cellpadding="0" style="width: 100%;">
    <tr>
      @foreach ($holidays as $date => $holidayData)
        <td
          style="border: 1px solid #ff8c00; padding: 8px; vertical-align: top; width: 200px; background-color: #fff3e0;">
          <strong>{{ $holidayData['name'] }}</strong><br>
          <small>{{ Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</small>
        </td>
        @if ($loop->iteration % 4 == 0 && !$loop->last)
    </tr>
    <tr>
      @endif
      @endforeach
    </tr>
  </table>

  <!-- Legend -->
  <table style="margin-top: 10px; font-size: 10px;">
    <tr>
      <td style="background-color: #16c472; padding: 5px; width: 30px;">H</td>
      <td style="padding: 5px;">Hadir Tepat Waktu</td>
      <td style="background-color: #ff8c00; padding: 5px; width: 30px; color: white;">H</td>
      <td style="padding: 5px;">Hari Libur Nasional</td>
      <td style="background-color: #e0e0e0; padding: 5px; width: 30px;">-</td>
      <td style="padding: 5px;">Tanggal Masa Depan</td>
    </tr>
    <tr>
      <td style="background-color: #ffcccc; padding: 5px; width: 30px;">A</td>
      <td style="padding: 5px;">Tidak Hadir</td>
      <td style="background-color: #d0ec6c; padding: 5px; width: 30px;">P</td>
      <td style="padding: 5px;">Pulang</td>
      <td style="color: red; padding: 5px; width: 30px;">*</td>
      <td style="padding: 5px;">Terlambat (Merah)</td>
    </tr>
  </table>

  <footer>
    <h5>Laporan ini dicetak tanggal: {{ now()->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }} WIB</h5>
    <h6>Dicetak oleh: {{ auth()->user()->name ?? 'Anonim' }}</h6>
  </footer>
</body>

</html>
