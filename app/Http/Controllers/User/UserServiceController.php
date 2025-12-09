<?php

namespace App\Http\Controllers\User;

use App\Models\Service;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserServiceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status', 'active'); // Default filter active

        $services = Service::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
                });
            })
            ->when($status === 'active', function ($query) {
                return $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query) {
                return $query->where('is_active', false);
            })
            ->orderBy('order')
            ->paginate(12);

        return view('user.services.index', [
            'services' => $services,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function create(Service $service)
    {
        return view('user.services.create', compact('service'));
    }
}
