<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Surat Peringatan</title>
  <style>
    @page {
  size: 210mm 330mm; /* F4 portrait */
  margin: 20mm;      /* bisa diganti sesuai kebutuhan */
}
    /* === Font Bookman Old Style === */
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

    /* Header dengan logo dan text sejajar */
    .header {
      display: table;
      width: 100%;
      line-height: 1.1;
      margin-bottom: 10px;
    }

    .header-logo {
      display: table-cell;
      width: 100px;
      vertical-align: middle;
      text-align: center;
      padding-right: 10px;
    }

    .header-text {
      display: table-cell;
      vertical-align: middle;
      text-align: center;
    }

    .title { font-weight: bold; font-size: 12pt; margin: 2px 0; }
    .title.besar { font-size: 14pt; }
    .title.textnagari { font-size: 18pt; }
    .kop-sub { font-size: 11pt; margin-top: 6px; }
    .hr {
        border-bottom: 3px solid #000;
        margin: 10px 0 -1px; /* Margin bottom negatif */
        }
    .hr2 {
        border-bottom: 1px solid #000;
        margin: 3px 0 18px; /* Tidak ada margin top */
        }

    .meta { width:100%; margin-bottom: 12px; }
    .meta .left { float:left; width:62%; font-size: 11pt; }
    .meta .right { float:right; width:36%; font-size: 11pt; text-align:left; }

    .penerima { margin-top: 12px; margin-bottom: 10px; }
    .body { text-align: justify; line-height: 1.6; margin-bottom: 12px; }

    .signature { margin-top: 30px; width:100%; }
    .signature .right { float:right; width:45%; text-align:center; }
    .signature .spasi { height: 80px; }

    .tembusan { margin-top: 10px; font-size: 11pt; }

    .clearfix::after { content: ""; clear: both; display: table; }
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
      <div class="kop-sub">Basung Kenagarian Sikucur – Kode Pos 25552</div>
      <div class="kop-sub" >
            <span style="font-size: 11pt; font-style: italic; color: blue;">https://www.sikucur.padangpariamankab.go.id
            </span>
            Email:
            <span style="font-size: 11pt; font-style: italic; color: blue;">nagari.sikucur@gmail.com</span>
      </div>
    </div>
  </div>

  <div class="hr"></div>
  <div class="hr2"></div>

  <div class="meta clearfix">
    <div class="left">
        <div>Nomor&nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ $nomor ?? '145 /     / NS/2019' }}</strong></div>
        <div>Lampiran&nbsp;&nbsp;: {{ $lampiran ?? '-' }}</div>
      <div>Perihal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <strong>{{ $perihal ?? 'Teguran I' }}</strong></div>
    </div>
    <div class="right">
      <div>{{ $kota ?? 'Basung' }}, {{ $tanggal ?? '2 September 2019' }}</div>
        <div class="penerima">
            Kepada Yth.:<br>
            <strong>{{ $kepada ?? 'Sdr. Pegawai Nagari Sikucur' }}</strong><br>
            di -<br>
            Tempat
        </div>
    </div>
  </div>

  <div style="clear:both;"></div>



  <div class="body">
    <p>Dengan hormat,</p>

    <p>
      Dalam rangka pelaksanaan amanat Pasal 55 Peraturan Bupati Padang Pariaman nomor 18 Tahun 2018
      tentang Susunan Organisasi dan Tata Kerja Pemerintah Nagari dan Manajemen Perangkat Nagari serta
      untuk lebih meningkatkan kinerja, maka dengan ini Saya selaku Wali Nagari Sikucur memerintahkan kepada Saudara:
    </p>

    <ol>
      <li>Agar Hadir Tepat Waktu Pada Jam Masuk Kantor sesuai hari dan jam kerja kantor.</li>
      <li>Bekerja agar lebih responsif, disiplin, professional dan bertanggung jawab sebagai pelaksana kegiatan.</li>
    </ol>

    <p>
      Jika hal tersebut diatas tidak dilaksanakan dengan baik dalam jangka tahun <strong>{{ $tahun ?? '3 hari' }}</strong> sejak surat ini dikeluarkan
      maka akan diberikan sanksi berikutnya.
    </p>

    <p>
      Demikian {{ $perihal ?? 'Teguran I' }} ini dibuat agar dapat dilaksanakan sebagaimana mestinya.
    </p>
  </div>

  <div class="signature clearfix">
    <div class="right">
      <div style="font-weight: bold;"> {{ $tempat_ttd ?? 'WALI NAGARI SIKUCUR,' }}</div>
      <div class="spasi"></div>
      <div><strong>{{ $penandatangan ?? 'ASRUL KHAIRI, A.Md' }}</strong></div>
    </div>
  </div>

  <div style="clear:both;"></div>

  <div class="tembusan">
    <strong>Tembusan:</strong> disampaikan kepada Yth:<br>
    1. Camat V Koto Kampung Dalam<br>
    2. Ketua Bamus Nagari Sikucur<br>
  </div>
</body>
</html>
