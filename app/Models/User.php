<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\WdmsModel;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    protected $connection = 'pgsql'; // atau sesuai koneksi yg ada di config/database.php
    protected $table = 'users';
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'slug',
        'jabatan_id',
        'emp_id',
        'nagari_id',
        'username',
        'image',
        'email',
        'no_hp',
        'no_ktp',
        'no_bpjs',
        'alamat',
        'aktif',
        'email_verified_at',
        'password_recovery',
    ];
    protected $casts = [
        'no_hp' => 'string',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function absensiGabunganPerTanggal($tanggal)
    {
        $isWorkingDay = true;

        if ($this->nagari && $this->nagari->workDays) {
            $dayName = Carbon::parse($tanggal)->format('l'); // Senin, Selasa, ...
            $workDay = $this->nagari->workDays()->where('day', $dayName)->first();

            // Cek null sebelum akses is_working_day
            if ($workDay && $workDay->is_working_day == false) {
                $isWorkingDay = false;
            }
        }

        // Ambil AbsensiPegawai
        $absensi = $this->absensiPegawai()
            ->whereDate('date_in', $tanggal)
            ->first();

        if ($absensi) {
            return (object)[
                'tanggal' => $absensi->date_in,
                'sn_mesin' => $absensi->sn_mesin,
                'sumber' => 'absensi',
                'absensi' => $absensi->absensi,
                'is_working_day' => $isWorkingDay,
            ];
        }

        // Ambil WDMS jika tidak ada absensi
        $wdms = WdmsModel::where('emp_id', $this->emp_id)
            ->whereDate('punch_time', $tanggal)
            ->first();

        if ($wdms) {
            return (object)[
                'tanggal' => $wdms->punch_time,
                'sn_mesin' => $wdms->sn,
                'sumber' => 'wdms',
                'absensi' => 'Hadir',
                'is_working_day' => $isWorkingDay,
            ];
        }

        // Jika kosong
        return (object)[
            'tanggal' => $tanggal,
            'sn_mesin' => null,
            'sumber' => null,
            'absensi' => null,
            'is_working_day' => $isWorkingDay,
        ];
    }
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
    public function wali()
    {
        return $this->belongsTo(Nagari::class, 'wali_id');
    }
    public function absensiPegawai(): HasMany
    {
        return $this->hasMany(AbsensiPegawai::class);
    }
    public function wdms()
    {
        return $this->hasMany(WdmsModel::class, 'emp_id', 'emp_id');
    }
    public function izin()
    {
        return $this->hasMany(IzinPegawai::class);
    }
    public function RekapAbsensiPegawai(): HasMany
    {
        return $this->hasMany(RekapAbsensiPegawai::class);
    }
}
