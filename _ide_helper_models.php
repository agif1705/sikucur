<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $absensi_by
 * @property int $emp_id
 * @property string|null $absensi
 * @property string|null $status_absensi
 * @property string|null $sn_mesin
 * @property int|null $accept
 * @property string|null $accept_by
 * @property int $user_id
 * @property int $nagari_id
 * @property string $time_in
 * @property string|null $time_out
 * @property string $date_in
 * @property string|null $date_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Nagari $nagari
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereAbsensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereAbsensiBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereAccept($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereAcceptBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereDateIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereDateOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereEmpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereSnMesin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereStatusAbsensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereTimeIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereTimeOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawai whereUserId($value)
 */
	class AbsensiPegawai extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $absensi_by
 * @property int $emp_id
 * @property string|null $absensi
 * @property string|null $status_absensi
 * @property string|null $sn_mesin
 * @property int|null $accept
 * @property string|null $accept_by
 * @property int $user_id
 * @property int $nagari_id
 * @property string $time_in
 * @property string|null $time_out
 * @property string $date_in
 * @property string|null $date_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\User|null $employee
 * @property-read \App\Models\Nagari $nagari
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereAbsensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereAbsensiBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereAccept($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereAcceptBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereDateIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereDateOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereEmpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereSnMesin($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereStatusAbsensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereTimeIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereTimeOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AbsensiPegawaiHarian whereUserId($value)
 */
	class AbsensiPegawaiHarian extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $absensi_by
 * @property string|null $absensi
 * @property string|null $keterangan_absensi
 * @property int|null $accept
 * @property string|null $accept_by
 * @property int $user_id
 * @property int $nagari_id
 * @property float $jadwal_latitude
 * @property float $jadwal_longitude
 * @property string $jadwal_start_time
 * @property string $jadwal_end_time
 * @property float $start_latitude
 * @property float $start_longitude
 * @property float|null $end_latitude
 * @property float|null $end_longitude
 * @property string $start_time
 * @property string|null $end_time
 * @property string $date_in
 * @property string|null $date_out
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Nagari $nagari
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAbsensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAbsensiBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAccept($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereAcceptBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDateIn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDateOut($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereEndLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereEndLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereJadwalEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereJadwalLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereJadwalLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereJadwalStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereKeteranganAbsensi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStartLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStartLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Attendance whereUserId($value)
 */
	class Attendance extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $jabatan_id
 * @property string $name
 * @property string $no_hp
 * @property string $coment
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Jabatan $jabatan
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereComent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereJabatanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Coments whereUpdatedAt($value)
 */
	class Coments extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FingerPrint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FingerPrint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FingerPrint query()
 */
	class FingerPrint extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $footer
 * @property string $token_wuzapi
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp whereFooter($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp whereTokenWuzapi($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FooterWhatsApp whereUpdatedAt($value)
 */
	class FooterWhatsApp extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormatSuratPelayanan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormatSuratPelayanan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormatSuratPelayanan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormatSuratPelayanan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormatSuratPelayanan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FormatSuratPelayanan whereUpdatedAt($value)
 */
	class FormatSuratPelayanan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $nagari_id
 * @property string $by
 * @property string $sender
 * @property int $send
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereSend($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereSender($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|HistorySendWa whereUserId($value)
 */
	class HistorySendWa extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Coments> $coments
 * @property-read int|null $coments_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Jabatan whereUpdatedAt($value)
 */
	class Jabatan extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $shift_id
 * @property int $nagari_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Nagari $nagari
 * @property-read \App\Models\Shift $shift
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser whereShiftId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|JadwalUser whereUserId($value)
 */
	class JadwalUser extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $alamat
 * @property string|null $sn_fingerprint
 * @property string|null $kop_surat
 * @property string|null $logo
 * @property float $longitude
 * @property float $latitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\TvInformasi|null $TvInformasi
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TvGaleri> $galeri
 * @property-read int|null $galeri_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WorkDay> $workDays
 * @property-read int|null $work_days_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereKopSurat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereSnFingerprint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Nagari whereUpdatedAt($value)
 */
	class Nagari extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Presensi whereUpdatedAt($value)
 */
	class Presensi extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property int $is_aktif
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereIsAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Shift whereUpdatedAt($value)
 */
	class Shift extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $nagari_id
 * @property string|null $name
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Nagari $nagari
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvGaleri whereUpdatedAt($value)
 */
	class TvGaleri extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $nagari_id
 * @property int $user_id
 * @property string|null $video
 * @property string|null $bupati
 * @property string|null $bupati_image
 * @property string|null $wakil_bupati
 * @property string|null $wakil_bupati_image
 * @property string|null $wali_nagari
 * @property string|null $wali_nagari_image
 * @property string|null $bamus
 * @property string|null $bamus_image
 * @property string|null $babinsa
 * @property string|null $babinsa_image
 * @property string|null $running_text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TvGaleri> $galeri
 * @property-read int|null $galeri_count
 * @property-read \App\Models\Nagari $nagari
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereBabinsa($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereBabinsaImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereBamus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereBamusImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereBupati($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereBupatiImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereRunningText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereVideo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereWakilBupati($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereWakilBupatiImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereWaliNagari($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TvInformasi whereWaliNagariImage($value)
 */
	class TvInformasi extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int $emp_id
 * @property int $jabatan_id
 * @property int $nagari_id
 * @property string $username
 * @property string|null $image
 * @property string $email
 * @property float|null $no_hp
 * @property string|null $no_ktp
 * @property string|null $no_bpjs
 * @property string|null $alamat
 * @property int $aktif
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string $password_recovery
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AbsensiPegawai> $absensiPegawai
 * @property-read int|null $absensi_pegawai_count
 * @property-read \App\Models\Jabatan $jabatan
 * @property-read \App\Models\Nagari $nagari
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\WdmsModel> $wdms
 * @property-read int|null $wdms_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAktif($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAlamat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereJabatanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoBpjs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoHp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereNoKtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePasswordRecovery($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $emp_code
 * @property string $punch_time
 * @property string $punch_state
 * @property int $verify_type
 * @property string|null $work_code
 * @property string|null $terminal_sn
 * @property string|null $terminal_alias
 * @property string|null $area_alias
 * @property float|null $longitude
 * @property float|null $latitude
 * @property string|null $gps_location
 * @property string|null $mobile
 * @property int|null $source
 * @property int|null $purpose
 * @property string|null $crc
 * @property int|null $is_attendance
 * @property string|null $reserved
 * @property string|null $upload_time
 * @property int|null $sync_status
 * @property string|null $sync_time
 * @property int|null $is_mask
 * @property string|null $temperature
 * @property int|null $emp_id
 * @property int|null $terminal_id
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereAreaAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereCrc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereEmpCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereEmpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereGpsLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereIsAttendance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereIsMask($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel wherePunchState($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel wherePunchTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel wherePurpose($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereReserved($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereSource($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereSyncStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereSyncTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereTemperature($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereTerminalAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereTerminalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereTerminalSn($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereUploadTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereVerifyType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WdmsModel whereWorkCode($value)
 */
	class WdmsModel extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property string $id
 * @property int $footer_whats_app_id
 * @property int $nagari_id
 * @property string $command
 * @property string $response
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\FooterWhatsApp|null $footer
 * @property-read \App\Models\Nagari $nagari
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereCommand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereFooterWhatsAppId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereResponse($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WhatsAppCommand whereUpdatedAt($value)
 */
	class WhatsAppCommand extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $nagari_id
 * @property string $day
 * @property int $is_working_day
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \App\Models\Nagari $nagari
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay whereDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay whereIsWorkingDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay whereNagariId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|WorkDay whereUpdatedAt($value)
 */
	class WorkDay extends \Eloquent {}
}

