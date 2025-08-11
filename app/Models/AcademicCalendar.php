<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AcademicCalendar extends Model
{
    protected $fillable = [
        'title',
        'academic_year',
        'file_path',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessor untuk mendapatkan URL PDF
    public function getPdfUrlAttribute()
    {
        if ($this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    // Accessor untuk mengecek apakah file exists
    public function getFileExistsAttribute()
    {
        return $this->file_path && Storage::disk('public')->exists($this->file_path);
    }
}
