<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Models\AcademicCalendar;
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
            $activeCalendar->pdf_url = Storage::disk('public')->url($activeCalendar->file_path);
        }

        return view('user.home.index', [
            'services' => $services,
            'activeCalendar' => $activeCalendar,
        ]);
    }
}
