<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengantar Wali Korong</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f6f7fb; color: #111; }
        .container { max-width: 720px; margin: 32px auto; background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 6px 24px rgba(0,0,0,0.08); }
        h1 { font-size: 20px; margin-bottom: 8px; }
        label { display: block; font-weight: 600; margin-top: 12px; }
        input, textarea, select { width: 100%; padding: 10px 12px; margin-top: 6px; border: 1px solid #d7dbe7; border-radius: 8px; }
        button { margin-top: 18px; background: #1a73e8; color: #fff; border: 0; padding: 12px 16px; border-radius: 8px; cursor: pointer; }
        .error { color: #b00020; font-size: 13px; }
        .success { background: #e8f5e9; color: #1b5e20; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .note { font-size: 13px; color: #555; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Form Surat Pengantar Wali Korong</h1>
        <p class="note">Mohon isi data dengan benar sesuai KTP.</p>

        @if (!empty($success))
            <div class="success">
                Surat pengantar berhasil disimpan.
                @if (!empty($downloadUrl))
                    <div style="margin-top: 8px;">
                        <a href="{{ $downloadUrl }}">Unduh PDF Surat Pengantar</a>
                    </div>
                @endif
            </div>
        @endif

        <form method="POST" action="{{ $submitUrl }}">
            @csrf

            <label for="pemohon_nik">NIK</label>
            <input id="pemohon_nik" name="pemohon_nik" value="{{ old('pemohon_nik', $pengantar->pemohon_nik) }}" />
            @error('pemohon_nik')<div class="error">{{ $message }}</div>@enderror

            <label for="pemohon_nama">Nama Lengkap</label>
            <input id="pemohon_nama" name="pemohon_nama" value="{{ old('pemohon_nama', $pengantar->pemohon_nama) }}" />
            @error('pemohon_nama')<div class="error">{{ $message }}</div>@enderror

            <label for="pemohon_alamat">Alamat</label>
            <textarea id="pemohon_alamat" name="pemohon_alamat" rows="3">{{ old('pemohon_alamat', $pengantar->pemohon_alamat) }}</textarea>
            @error('pemohon_alamat')<div class="error">{{ $message }}</div>@enderror

            <label for="pemohon_alamat_domisili">Alamat Lengkap Domisili</label>
            <textarea id="pemohon_alamat_domisili" name="pemohon_alamat_domisili" rows="3">{{ old('pemohon_alamat_domisili', $pengantar->pemohon_alamat_domisili) }}</textarea>
            <p class="note">Isi jika alamat domisili berbeda dari alamat KTP.</p>
            @error('pemohon_alamat_domisili')<div class="error">{{ $message }}</div>@enderror

            <label for="pemohon_telepon">Telepon</label>
            <input id="pemohon_telepon" name="pemohon_telepon" value="{{ old('pemohon_telepon', $pengantar->pemohon_telepon) }}" />
            @error('pemohon_telepon')<div class="error">{{ $message }}</div>@enderror

            <label for="korong">Korong/Wilayah</label>
            <select id="korong" name="korong">
                <option value="">-- Pilih Korong --</option>
                @foreach ($wilayahs as $wilayah)
                    <option value="{{ $wilayah }}" {{ old('korong', $pengantar->korong) === $wilayah ? 'selected' : '' }}>
                        {{ $wilayah }}
                    </option>
                @endforeach
            </select>
            @error('korong')<div class="error">{{ $message }}</div>@enderror

            <label for="keperluan">Keperluan</label>
            <textarea id="keperluan" name="keperluan" rows="3">{{ old('keperluan', $pengantar->keperluan) }}</textarea>
            @error('keperluan')<div class="error">{{ $message }}</div>@enderror

            <label for="tanggal_pengantar">Tanggal Surat</label>
            <input id="tanggal_pengantar" name="tanggal_pengantar" type="date" value="{{ old('tanggal_pengantar', optional($pengantar->tanggal_pengantar)->format('Y-m-d')) }}" />
            @error('tanggal_pengantar')<div class="error">{{ $message }}</div>@enderror

            <button type="submit">Simpan Surat Pengantar</button>
        </form>
    </div>
</body>
</html>
