<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserServiceController extends Controller
{
    public function index()
    {
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('user.services.index', compact('services'));
    }

    /**
     * Show the form to create a service request
     */
    public function create(Service $service)
    {
        if (!$service->is_active) {
            abort(404);
        }

        return view('user.services.create', compact('service'));
    }
}
