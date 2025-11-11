<?php

namespace App\Models;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'nim',    // For mahasiswa
        'nip',   // For dosen
        'jabatan' // For dosen

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
    // Mahasiswa yang mengajukan surat aktif kuliah
    public function suratAktifKuliah()
    {
        return $this->hasMany(SuratAktifKuliah::class, 'mahasiswa_id');
    }

    // Jika user juga bisa sebagai penandatangan
    public function suratDitandatangani()
    {
        return $this->hasMany(SuratAktifKuliah::class, 'penandatangan_id');
    }


    // Scope for mahasiswa
    public function scopeMahasiswa($query)
    {
        return $query->role('mahasiswa');
    }

    // Scope for dosen
    public function scopeDosen($query)
    {
        return $query->role('dosen');
    }

    // Scope for staff
    public function scopeStaff($query)
    {
        return $query->role('staff');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            if (empty($user->getRoleNames())) {
                Role::firstOrCreate(['name' => 'mahasiswa']);
                $user->assignRole('mahasiswa');
            }
        });
    }


    public function pembayaranUkt()
    {
        return $this->hasMany(PembayaranUkt::class, 'mahasiswa_id');
    }

    public function isMahasiswa()
    {
        return $this->hasRole('mahasiswa');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPasswordNotification($token));
    }

    public function isDosen()
    {
        return $this->hasRole('dosen');
    }

    /**
     * Check if user is dosen with approval authority (Koordinator Prodi or Pimpinan)
     */
    public function isDosenWithApprovalAuthority()
    {
        if (!$this->isDosen()) {
            return false;
        }

        $approvalJabatan = [
            'koordinator program studi',
            'pimpinan jurusan ptik',
            'ketua jurusan ptik', // tambahan jika ada variasi nama
            'kaprodi', // tambahan jika ada singkatan
        ];

        return in_array(strtolower($this->jabatan ?? ''), $approvalJabatan);
    }

    /**
     * Check if user is regular dosen (without approval authority)
     */
    public function isRegularDosen()
    {
        return $this->isDosen() && !$this->isDosenWithApprovalAuthority();
    }
}
