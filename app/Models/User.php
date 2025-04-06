<?php

namespace App\Models;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'nidn',   // For dosen

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
}
