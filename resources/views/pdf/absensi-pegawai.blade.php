<!DOCTYPE html>
<html>

<head>
  <style>
    @page {
      size: legal landscape;
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

    .holiday-info {
      margin-top: 15px;
      padding: 10px;
      background-color: #fff3e0;
      border: 1px solid #ff8c00;
      border-radius: 5px;
    }

    .holiday-info h4 {
      margin: 0 0 10px 0;
      color: #ff8c00;
      font-size: 12px;
    }

    .holiday-list {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .holiday-item {
      background-color: #ff8c00;
      color: white;
      padding: 5px 10px;
      border-radius: 3px;
      font-size: 10px;
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
            @if ($isHolidayApi && isset($holidays[$date]['name'])) title="{{ $holidays[$date]['name'] }}" @endif>
            {{ $dateObj->format('d') }}<br>
            {{ $dateObj->translatedFormat('D') }}
            @if ($isHolidayApi && isset($holidays[$date]['name']))
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
  <!-- Keterangan Hari Libur Nasional -->
  @if (count($holidays) > 0)
    <div class="holiday-info">
      <h4>🏛️ Hari Libur Nasional Bulan {{ $monthName }}</h4>
      <div class="holiday-list">
        @foreach ($holidays as $date => $holidayData)
          @if (is_array($holidayData) && isset($holidayData['name']))
            <div class="holiday-item">
              <strong>{{ $holidayData['name'] }}</strong><br>
              <small>{{ Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</small>
            </div>
          @endif
        @endforeach
      </div>
    </div>
  @else
    <div class="holiday-info">
      <h4>📅 Tidak ada hari libur nasional pada bulan {{ $monthName }}</h4>
    </div>
  @endif

  <footer>
    <h5>Laporan ini dicetak tanggal: {{ now()->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }} WIB</h5>
    <h6>Dicetak oleh: {{ auth()->user()->name ?? 'Anonim' }}</h6>
  </footer>
</body>

</html>
