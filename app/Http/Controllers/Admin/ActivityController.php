<?php

namespace App\Http\Controllers\Admin;

use App\Models\TrackingSurat;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    public function index()
    {
        $activities = TrackingSurat::with(['mahasiswa', 'surat'])
            ->latest()
            ->paginate(15);
        return view('admin.activities.index', compact('activities'));
    }
}