<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            size: 210mm 330mm;
            margin: 15mm;
        }

        @font-face {
            font-family: 'BookmanOldStyle';
            src: url('{{ public_path('fonts/BOOKOS.TTF') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'BookmanOldStyle', serif;
            font-size: 12pt;
            line-height: 1.4;
            margin: 5px;
            color: #000;
        }

        .header { display: table; width: 100%; line-height: 1.1; margin-bottom: 10px; }
        .header-logo { display: table-cell; width: 100px; vertical-align: middle; text-align: center; padding-right: 10px; }
        .header-text { display: table-cell; vertical-align: middle; text-align: center; }

        .title { font-weight: bold; font-size: 12pt; margin: 2px 0; text-align: center; }
        .title.besar { font-size: 14pt; }
        .title.textnagari { font-size: 18pt; }
        .kop-sub { font-size: 11pt; margin-top: 6px; }

        .hr  { border-bottom: 3px solid #000; margin: 10px 0 -1px; }
        .hr2 { border-bottom: 1px solid #000; margin: 3px 0 18px; }

        .judul-surat { text-align: center; margin: 0 0 16px; }
        .judul-surat h2 { font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 4px 6px; vertical-align: top; }
        .label { width: 32%; }
        .value { width: 68%; }
        .signature { margin-top: 30px; width: 100%; }
        .signature .right { float: right; width: 45%; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-logo">
            <img src="{{ $logo }}" alt="Logo" height="120" width="110" />
        </div>
        <div class="header-text">
            <div class="title besar">PEMERINTAH KABUPATEN PADANG PARIAMAN</div>
            <div class="title">KECAMATAN V KOTO KAMPUNG DALAM</div>
            <div class="title textnagari">NAGARI SIKUCUR</div>
            <div class="kop-sub">Basung Kenagarian Sikucur &ndash; Kode Pos 25552</div>
            <div class="kop-sub">
                <span style="font-style: italic; color: blue;">https://www.sikucur.padangpariamankab.go.id</span>
                &nbsp; Email: <span style="font-style: italic; color: blue;">nagari.sikucur@gmail.com</span>
            </div>
        </div>
    </div>

    <div class="hr"></div>
    <div class="hr2"></div>

    <div class="judul-surat">
        <h2>Surat Pengantar Wali Korong</h2>
    </div>

    @php
        $nama = $pengantar?->pemohon_nama ?? '................................';
        $nik = $pengantar?->pemohon_nik ?? '................................';
        $alamat = $pengantar?->pemohon_alamat ?? '................................';
        $alamatDomisili = $pengantar?->pemohon_alamat_domisili ?: ($pengantar?->pemohon_alamat ?? '................................');
        $telepon = $pengantar?->pemohon_telepon ?? '................................';
        $korong = $pengantar?->korong ?? '................................';
        $keperluan = $pengantar?->keperluan ?? '................................';
        $tanggal = $pengantar?->tanggal_pengantar?->format('d-m-Y') ?? '..............';
        $wali = $pengantar?->waliKorong?->name ?? '................................';
    @endphp

    <p>
        Yang bertanda tangan dibawah ini Wali Korong {{ $korong }} Nagari Sikucur memberikan surat pengantar kepada:
    </p>

    <table>
        <tr>
            <td class="label">Nama</td>
            <td class="value">: {{ $nama }}</td>
        </tr>
        <tr>
            <td class="label">NIK</td>
            <td class="value">: {{ $nik }}</td>
        </tr>
        <tr>
            <td class="label">Alamat KTP</td>
            <td class="value">: {{ $alamat }}</td>
        </tr>
        <tr>
            <td class="label">Alamat Domisili</td>
            <td class="value">: {{ $alamatDomisili }}</td>
        </tr>
        <tr>
            <td class="label">Telepon</td>
            <td class="value">: {{ $telepon }}</td>
        </tr>
        <tr>
            <td class="label">Korong/Wilayah</td>
            <td class="value">: {{ $korong }}</td>
        </tr>
        <tr>
            <td class="label">Dalam hal Pengurusan </td>
            <td class="value">: {{ $keperluan }}</td>
        </tr>
    </table>

    <p>Demikian surat pengantar ini dibuat untuk dipergunakan sebagaimana mestinya.</p>

    <div class="signature">
        <div class="right">
            <div>{{ $korong }}, {{ $tanggal }}</div>
            <div>Wali Korong {{ $korong }}</div>
            <br><br><br>
            <div><strong>{{ $wali }}</strong></div>
        </div>
    </div>
</body>
</html>
