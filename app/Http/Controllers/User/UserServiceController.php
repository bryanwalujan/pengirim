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
            ->paginate(9)
            ->withQueryString();

        return view('user.services.index', [
            'services' => $services,
            'search' => $search,
            'status' => $status
        ]);
    }

    public function search(Request $request)
    {
        // Only accept AJAX requests
        if (!$request->ajax()) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $search = $request->input('search');
        $status = $request->input('status', 'active');
        $page = $request->input('page', 1);

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
            ->paginate(10)
            ->setPath(route('user.services.index'))
            ->withQueryString();

        // Render service cards HTML
        $servicesHtml = view('user.services.partials.service-cards', [
            'services' => $services
        ])->render();

        // Render pagination HTML
        $paginationHtml = view('user.services.partials.pagination', [
            'services' => $services
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $servicesHtml,
            'pagination' => $paginationHtml,
            'count' => $services->count(),
            'total' => $services->total(),
            'hasResults' => $services->count() > 0,
            'search' => $search
        ]);
    }

    public function create(Service $service)
    {
        return view('user.services.create', compact('service'));
    }
}
