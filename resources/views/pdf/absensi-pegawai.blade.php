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
            @endphp
            <td class="@if ($isHoliday) holiday @endif">
              <div class="time-entry masuk @if ($attendance['masuk'] == 'A' && !$isHoliday) empty @endif">

                <span style="@if ($attendance['is_late']) color:red @endif">{{ $attendance['masuk'] }}</span>

              </div>
              <div class="time-entry pulang @if ($attendance['pulang'] == '-' && !$isHoliday) empty @endif">
                {{ $attendance['pulang'] }}
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
  <table border="0" cellspacing="10" cellpadding="0" style="width: 100%;">
    <tr>
      @foreach ($holidays as $name => $date)
        <td style="border: 1px solid #ddd; padding: 8px; vertical-align: top; width: 200px;">
          <strong>{{ $name }}</strong><br>
          {{ $date }}
        </td>
        @if ($loop->iteration % 4 == 0 && !$loop->last)
    </tr>
    <tr>
      @endif
      @endforeach
    </tr>
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
  <footer>
    <h5>Laporan ini di cetak tanggal {{ now() }}</h5>
    <h6>Dicetak ole Pegawai: {{ auth()->user()->name ?? "Anonim" }}</h6>
  </footer>
</body>

</html>
