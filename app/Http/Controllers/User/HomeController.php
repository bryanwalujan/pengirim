<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        // Get 6 active services to display on homepage
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->take(6)
            ->get();

        return view('user.home.index', [
            'services' => $services,
        ]);
    }
}
