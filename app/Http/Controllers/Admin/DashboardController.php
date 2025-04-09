<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DashboardController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('view dashboard'); // Pastikan user memiliki izin untuk mengelola dashboard
        return view('admin.dashboard.index');
    }
}
