<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use App\Models\StatusSurat;
use App\Models\SuratPindah;
use Illuminate\Http\Request;
use App\Models\SuratIjinSurvey;
use App\Models\AcademicCalendar;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)
            ->orderBy('order')
            ->take(6)
            ->get();

        $activeCalendar = AcademicCalendar::where('is_active', true)->first();

        // Tambahkan full URL untuk file PDF
        if ($activeCalendar) {
            $activeCalendar->pdf_url = asset('storage/' . $activeCalendar->file_path);
        }

        // Get dynamic counts for each letter type
        $letterCounts = [
            'aktif_kuliah' => $this->getLetterCount(SuratAktifKuliah::class),
            'ijin_survey' => $this->getLetterCount(SuratIjinSurvey::class),
            'cuti_akademik' => $this->getLetterCount(SuratCutiAkademik::class),
            'pindah' => $this->getLetterCount(SuratPindah::class),
        ];

        return view('user.home.index', [
            'services' => $services,
            'activeCalendar' => $activeCalendar,
            'letterCounts' => $letterCounts,
        ]);
    }

    protected function getLetterCount($letterType)
    {
        return cache()->remember("letter_count_{$letterType}", now()->addHours(6), function () use ($letterType) {
            return StatusSurat::where('surat_type', $letterType)
                ->whereIn('status', ['sudah_diambil'])
                ->count();
        });
    }
}
