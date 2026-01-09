<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PeminjamanProyektor;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class AdminPeminjamanProyektorController extends Controller
{
    public function index(Request $request)
    {
        // Update: Hapus 'phone' dari select karena kolom tidak ada
        $query = PeminjamanProyektor::with('user:id,name,nim,email')->latest();

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->status($request->status);
        }

        // Filter berdasarkan kode proyektor
        if ($request->filled('proyektor')) {
            $query->where('proyektor_code', 'like', '%' . $request->proyektor . '%');
        }

        // Search by name or NIM
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        $peminjaman = $query->paginate(20)->withQueryString();

        // Statistics
        $statistics = [
            'total' => PeminjamanProyektor::count(),
            'dipinjam' => PeminjamanProyektor::status('dipinjam')->count(),
            'dikembalikan' => PeminjamanProyektor::status('dikembalikan')->count(),
            'proyektor_tersedia' => count(config('proyektor.list', [])) - PeminjamanProyektor::status('dipinjam')
                ->distinct('proyektor_code')
                ->count('proyektor_code'),
        ];

        // Get proyektor list
        $proyektorList = config('proyektor.list', []);

        return view('admin.peminjaman-proyektor.index', compact(
            'peminjaman',
            'statistics',
            'proyektorList'
        ));
    }

    /**
     * Display the specified peminjaman detail (for AJAX)
     */
    public function show(PeminjamanProyektor $peminjamanProyektor)
    {
        if (request()->wantsJson() || request()->ajax()) {
            // Update: Hapus 'phone' dari select
            $peminjamanProyektor->load('user:id,name,nim,email');

            return view('admin.peminjaman-proyektor.detail-modal', [
                'peminjaman' => $peminjamanProyektor
            ])->render();
        }

        // If not AJAX, redirect to index with modal open
        return redirect()->route('admin.peminjaman-proyektor.index', ['open' => $peminjamanProyektor->id]);
    }

    /**
     * Delete peminjaman record (for AJAX)
     */
    public function destroy(PeminjamanProyektor $peminjamanProyektor)
    {
        try {
            $peminjamanProyektor->delete();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data peminjaman berhasil dihapus.'
                ]);
            }

            return redirect()->route('admin.peminjaman-proyektor.index')
                ->with('success', 'Data peminjaman berhasil dihapus.');
        } catch (\Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus data peminjaman: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('admin.peminjaman-proyektor.index')
                ->with('error', 'Gagal menghapus data peminjaman: ' . $e->getMessage());
        }
    }

    /**
     * Get proyektor management page
     */
    public function proyektorManagement()
    {
        $proyektorList = config('proyektor.list', []);
        $stats = [];

        foreach ($proyektorList as $code) {
            $stats[$code] = [
                'total_peminjaman' => PeminjamanProyektor::where('proyektor_code', $code)->count(),
                'sedang_dipinjam' => PeminjamanProyektor::where('proyektor_code', $code)
                    ->status('dipinjam')
                    ->exists(),
            ];
        }

        return view('admin.peminjaman-proyektor.proyektor-management', compact('proyektorList', 'stats'));
    }

    /**
     * Update proyektor list
     */
    public function updateProyektorList(Request $request)
    {
        $validated = $request->validate([
            'proyektor_codes' => 'required|array|min:1',
            'proyektor_codes.*' => 'required|string|max:20|regex:/^[A-Z0-9-]+$/i|distinct',
        ], [
            'proyektor_codes.required' => 'Minimal harus ada 1 proyektor',
            'proyektor_codes.*.regex' => 'Kode proyektor hanya boleh berisi huruf, angka, dan tanda hubung',
            'proyektor_codes.*.distinct' => 'Kode proyektor tidak boleh duplikat',
        ]);

        try {
            // Clean and uppercase codes, remove empty values
            $codes = array_values(array_filter(array_map(function ($code) {
                return strtoupper(trim($code));
            }, $validated['proyektor_codes'])));

            // Remove duplicates
            $codes = array_unique($codes);

            if (empty($codes)) {
                return redirect()->back()
                    ->with('error', 'Minimal harus ada 1 proyektor yang valid.');
            }

            // Update config file
            $configPath = config_path('proyektor.php');
            $content = "<?php\n\nreturn [\n    'list' => " . var_export($codes, true) . ",\n\n    'max_borrowing_days' => 7,\n];\n";

            if (!file_put_contents($configPath, $content)) {
                throw new \Exception('Gagal menulis file konfigurasi');
            }

            // Clear config cache
            Artisan::call('config:clear');

            return redirect()->route('admin.peminjaman-proyektor.proyektor-management')
                ->with('success', 'Daftar proyektor berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui daftar proyektor: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Override return peminjaman (for staff/admin)
     */
    public function overrideReturn(Request $request, PeminjamanProyektor $peminjamanProyektor)
    {
        // Check if already returned
        if ($peminjamanProyektor->status === 'dikembalikan') {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Peminjaman ini sudah dikembalikan.'
                ], 400);
            }

            return redirect()->back()->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        $validated = $request->validate([
            'catatan_override' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $peminjamanProyektor->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => now(),
                'catatan' => $validated['catatan_override']
                    ? 'OVERRIDE oleh staff: ' . $validated['catatan_override']
                    : 'OVERRIDE oleh staff pada ' . now()->translatedFormat('d M Y H:i'),
            ]);

            DB::commit();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Peminjaman berhasil dikembalikan (override).',
                    'data' => [
                        'tanggal_kembali' => $peminjamanProyektor->tanggal_kembali->translatedFormat('l, d M Y H:i'),
                        'catatan' => $peminjamanProyektor->catatan,
                    ]
                ]);
            }

            return redirect()->route('admin.peminjaman-proyektor.index')
                ->with('success', 'Peminjaman berhasil dikembalikan (override).');

        } catch (\Exception $e) {
            DB::rollBack();

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal override pengembalian: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Gagal override pengembalian: ' . $e->getMessage());
        }
    }
}