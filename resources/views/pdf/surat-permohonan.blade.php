<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>{{ $permohonan->pemohon_judul_surat ?? 'Surat' }} - {{ $permohonan->nomor_permohonan }}</title>
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
      margin: 5px;
      color: #000;
    }

    .header { display: table; width: 100%; line-height: 1.1; margin-bottom: 10px; }
    .header-logo { display: table-cell; width: 100px; vertical-align: middle; text-align: center; padding-right: 10px; }
    .header-text { display: table-cell; vertical-align: middle; text-align: center; }

    .title { font-weight: bold; font-size: 12pt; margin: 2px 0; }
    .title.besar { font-size: 14pt; }
    .title.textnagari { font-size: 18pt; }
    .kop-sub { font-size: 11pt; margin-top: 6px; }

    .hr  { border-bottom: 3px solid #000; margin: 10px 0 -1px; }
    .hr2 { border-bottom: 1px solid #000; margin: 3px 0 18px; }

    .judul-surat { text-align: center; margin: 0 0 16px; }
    .judul-surat h2 { font-size: 14pt; font-weight: bold; text-transform: uppercase; text-decoration: underline; }
    .nomor-surat { text-align: center; margin-bottom: 16px; font-size: 11pt; }

    .isi-surat { text-align: justify; line-height: 1.6; margin-bottom: 12px; font-size: 12pt; }
    .isi-surat p { margin-bottom: 8px; }
    .isi-surat table { width: 100%; border-collapse: collapse; margin: 8px 0; }
    .isi-surat td { padding: 3px 6px; vertical-align: top; }
    .isi-surat .label { font-weight: normal; width: 160px; }

    .signature { margin-top: 30px; width: 100%; }
    .signature .right { float: right; width: 45%; text-align: center; }
    .signature .spasi { height: 80px; }

    .info-strip { margin-top: 20px; padding: 6px 10px; border: 1px solid #ccc; font-size: 9pt; color: #555; background: #f9f9f9; }
    .info-strip table { width: 100%; border-collapse: collapse; }
    .info-strip td { padding: 2px 6px; }
    .info-strip .label { font-weight: bold; width: 130px; }

    .clearfix::after { content: ""; clear: both; display: table; }

    .watermark {
        position: fixed;
        top: 38%;
        left: -5%;
        width: 110%;
        text-align: center;
        font-size: 90pt;
        font-weight: bold;
        color: #8f3838;
        opacity: 0.45;
        transform: rotate(-45deg);
        z-index: 9999;
        pointer-events: none;
    }
  </style>
</head>
<body>
  @if(($permohonan->status?->kode_status ?? '') !== 'DONE')
  <div class="watermark">DRAFT SURAT</div>
  @endif

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

  @if($permohonan->suratPengantar)
  <div class="info-strip">
    <table>
      <tr>
        <td class="label">Surat Pengantar</td>
        <td>: Wali Korong {{ $permohonan->suratPengantar->korong ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Diketahui Wali</td>
        <td>: {{ $permohonan->suratPengantar->waliKorong?->name ?? '-' }}</td>
      </tr>
      <tr>
        <td class="label">Keperluan</td>
        <td>: {{ $permohonan->suratPengantar->keperluan ?? '-' }}</td>
      </tr>
    </table>
  </div>
  @endif

    {{-- ISI SURAT (dari template yang sudah di-replace placeholder) --}}
    @if($permohonan->pemohon_template)
        <div class="isi-surat">
            {!! $permohonan->pemohon_template !!}
        </div>
    @else
        {{-- Fallback jika template kosong --}}
        <div class="judul-surat">
            <h2>{{ strtoupper($permohonan->pemohon_judul_surat ?? $permohonan->jenisSurat?->nama_jenis ?? 'SURAT KETERANGAN') }}</h2>
        </div>
        <div class="nomor-surat">
            Nomor : {{ $permohonan->nomor_permohonan }}
        </div>
        <div class="isi-surat">
            <p>Yang bertanda tangan di bawah ini, Wali Nagari Sikucur Kecamatan V Koto Kampung Dalam
            Kabupaten Padang Pariaman, dengan ini menerangkan bahwa:</p>

            <table>
                <tr><td class="label">Nama</td><td>: {{ $permohonan->pemohon_nama }}</td></tr>
                <tr><td class="label">NIK</td><td>: {{ $permohonan->pemohon_nik }}</td></tr>
                <tr><td class="label">Tempat / Tgl Lahir</td><td>: {{ $permohonan->pemohon_tempat_lahir }}, {{ $permohonan->pemohon_tanggal_lahir ? \Carbon\Carbon::parse($permohonan->pemohon_tanggal_lahir)->format('d F Y') : '-' }}</td></tr>
                <tr><td class="label">Jenis Kelamin</td><td>: {{ $permohonan->pemohon_jk ? 'Laki-laki' : 'Perempuan' }}</td></tr>
                <tr><td class="label">Alamat KTP</td><td>: {{ $permohonan->pemohon_alamat }}</td></tr>
                <tr><td class="label">Alamat Domisili</td><td>: {{ $permohonan->pemohon_alamat_domisili ?: $permohonan->pemohon_alamat }}</td></tr>
                <tr><td class="label">Agama</td><td>: {{ $permohonan->pemohon_agama }}</td></tr>
            </table>

            <p style="margin-top:12px;">
                Adalah benar penduduk Nagari Sikucur dan mengajukan
                <strong>{{ $permohonan->jenisSurat?->nama_jenis }}</strong>
                untuk keperluan: <em>{{ $permohonan->keperluan ?? '-' }}</em>.
            </p>
            <p>Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.</p>
        </div>
    @endif

  @if($permohonan->PejabatTandaTangan_nama)
  <div class="signature clearfix">
    <div class="right">
      <div>Basung, {{ $permohonan->tanggal_permohonan
        ? \Carbon\Carbon::parse($permohonan->tanggal_permohonan)->translatedFormat('d F Y')
        : now()->translatedFormat('d F Y') }}</div>
      <div style="font-weight: bold;">
       <strong>@if ($permohonan->PejabatTandaTangan_jabatan == 'WaliNagari')
            Wali Nagari Sikucur
           
       @else
           An. Wali Nagari Sikucur
       @endif
      </strong>
      <br />
      @if ($permohonan->PejabatTandaTangan_jabatan !== 'WaliNagari')
            {{ $permohonan->PejabatTandaTangan_jabatan }}
       @else
           <br />
           <br />
       @endif</div>
      <div class="spasi"></div>
      <div><strong>{{ $permohonan->PejabatTandaTangan_nama }}</strong></div>
    </div>
  </div>
  <div style="clear: both;"></div>
  @endif
</body>
</html>
