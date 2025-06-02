<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServiceController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $this->authorize('manage services');
        $services = Service::orderBy('order')->paginate(5); // 10 items per page
        return view('admin.services.index', compact('services'));
    }

    public function create(Service $service)
    {
        // Set default order to be after the last service
        $service->order = Service::max('order') + 1;
        $service->is_active = true; // Default active
        return view('admin.services.create', compact('service'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'description' => 'nullable|string',
            'order' => 'required|integer',
            'is_active' => 'sometimes|boolean'
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['is_active'] = $request->has('is_active');

        Service::create($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil ditambahkan');
    }

    public function edit(Service $service)
    {
        return view('admin.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:50',
            'description' => 'nullable|string',
            'order' => 'required|integer',
            'is_active' => 'sometimes|boolean'
        ]);

        $validated['slug'] = Str::slug($request->name);
        $validated['is_active'] = $request->has('is_active');

        $service->update($validated);

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil diperbarui');
    }
    public function updateOrder(Request $request)
    {
        $request->validate([
            'services' => 'required|array',
            'services.*.id' => 'required|exists:services,id',
            'services.*.order' => 'required|integer'
        ]);

        DB::transaction(function () use ($request) {
            foreach ($request->services as $service) {
                Service::where('id', $service['id'])
                    ->update(['order' => $service['order']]);
            }
        });

        return response()->json(['success' => true]);
    }

    public function destroy(Service $service)
    {
        $service->delete();

        // Reorder remaining services
        Service::where('order', '>', $service->order)
            ->decrement('order');

        return redirect()->route('admin.services.index')
            ->with('success', 'Layanan berhasil dihapus');
    }
}
