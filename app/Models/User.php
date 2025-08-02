<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\WdmsModel;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasRoles, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $connection = 'mysql';

    protected $fillable = [
        'name',
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
    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class);
    }
    public function nagari()
    {
        return $this->belongsTo(Nagari::class);
    }
    public function absensiPegawai(): HasMany
    {
        return $this->hasMany(AbsensiPegawai::class);
    }
    public function wdms()
    {

        return $this->hasMany(WdmsModel::class, 'emp_code', 'emp_id');
    }
}
