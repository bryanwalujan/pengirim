<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
