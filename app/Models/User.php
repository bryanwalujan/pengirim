<?php

namespace App\Models;

use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

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
        'jabatan', // For dosen
        'status_aktif', // For mahasiswa (From TI Unima API)
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

    /**
     * Statistik pembimbing skripsi per tahun ajaran
     */
    public function statistikPembimbing()
    {
        return $this->hasMany(StatistikPembimbingSkripsi::class, 'dosen_id');
    }

    /**
     * Pengajuan SK dimana user adalah PS1
     */
    public function pengajuanSkSebagaiPs1()
    {
        return $this->hasMany(PengajuanSkPembimbing::class, 'dosen_pembimbing_1_id');
    }

    /**
     * Pengajuan SK dimana user adalah PS2
     */
    public function pengajuanSkSebagaiPs2()
    {
        return $this->hasMany(PengajuanSkPembimbing::class, 'dosen_pembimbing_2_id');
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
     * Check if user is regular dosen (without approval authority)
     */
    public function isRegularDosen()
    {
        return $this->isDosen() && ! $this->isDosenWithApprovalAuthority();
    }

    /**
     * Check if user is Koordinator Prodi / Kaprodi
     */
    public function isKoordinatorProdi(): bool
    {
        if (! $this->hasRole('dosen')) {
            return false;
        }

        $jabatanLower = strtolower($this->jabatan ?? '');

        return Str::contains($jabatanLower, [
            'koordinator program studi',
            'koordinator prodi',
            'kaprodi',
            'korprodi',
        ]);
    }

    /**
     * Check if user is Ketua Jurusan / Kajur
     */
    public function isKetuaJurusan(): bool
    {
        if (! $this->hasRole('dosen')) {
            return false;
        }

        if (! $this->jabatan) {
            return false;
        }

        $jabatanLower = strtolower($this->jabatan);

        // ✅ PERBAIKAN: Tambahkan semua variasi jabatan Kajur
        $keywords = [
            'ketua jurusan',
            'kajur',
            'kepala jurusan',
            'pimpinan jurusan',
            'pimpinan jurusan ptik', // Spesifik untuk kasus Anda
            'ketua jur',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($jabatanLower, $keyword)) {
                Log::info('Kajur detected', [
                    'user_id' => $this->id,
                    'jabatan' => $this->jabatan,
                    'matched_keyword' => $keyword,
                ]);

                return true;
            }
        }

        Log::warning('Not Kajur', [
            'user_id' => $this->id,
            'jabatan' => $this->jabatan,
        ]);

        return false;
    }

    /**
     * Check if user is Dosen with Approval Authority (Kaprodi or Kajur)
     */
    public function isDosenWithApprovalAuthority(): bool
    {
        return $this->hasRole('dosen') &&
            ($this->isKoordinatorProdi() || $this->isKetuaJurusan());
    }

    /**
     * Check if user is Dekan Fakultas
     */
    public function isDekan(): bool
    {
        if (! $this->hasRole('dosen')) {
            return false;
        }

        if (! $this->jabatan) {
            return false;
        }

        $jabatanLower = strtolower($this->jabatan);

        // Exclude Wakil Dekan
        if (str_contains($jabatanLower, 'wakil')) {
            return false;
        }

        $keywords = [
            'dekan fakultas',
            'dekan',
            'pimpinan fakultas',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($jabatanLower, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can sign as Panitia Sekretaris (Korprodi)
     */
    public function canSignAsPanitiaSekretaris(): bool
    {
        return $this->isKoordinatorProdi();
    }

    /**
     * Check if user can sign as Panitia Ketua (Dekan)
     */
    public function canSignAsPanitiaKetua(): bool
    {
        return $this->isDekan();
    }

    /**
     * Get user's jabatan display name
     */
    public function getJabatanDisplayAttribute(): string
    {
        if ($this->isKoordinatorProdi()) {
            return 'Koordinator Program Studi';
        }

        if ($this->isKetuaJurusan()) {
            return 'Ketua Jurusan';
        }

        if ($this->isDekan()) {
            return 'Dekan Fakultas';
        }

        return $this->jabatan ?? 'Dosen';
    }

    public function getStatusAktifTextAttribute()
    {
        return match($this->status_aktif) {
            'A' => 'Aktif',
            'L' => 'Lulus',
            'C' => 'Cuti',
            'N' => 'Non-Aktif',
            'K' => 'Keluar',
            default => '-',
        };
    }

    public function getStatusAktifBadgeColorAttribute()
    {
        return match($this->status_aktif) {
            'A' => 'success', // Hijau
            'L' => 'info',    // Biru Muda
            'C' => 'warning', // Kuning
            'N' => 'danger',  // Merah
            'K' => 'dark',    // Hitam/Abu Gelap
            default => 'secondary',
        };
    }
}
