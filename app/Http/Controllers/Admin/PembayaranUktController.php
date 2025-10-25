<?php
// filepath: app/Http/Controllers/Admin/PembayaranUktController.php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\PembayaranUkt;
use App\Exports\UktPaymentExport;
use App\Imports\UktPaymentImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class PembayaranUktController extends Controller
{
    public function index(Request $request)
    {
        // Get active academic year
        $tahunAjaranAktif = Cache::remember('tahun_ajaran_aktif', 3600, function () {
            return TahunAjaran::where('status_aktif', true)->first();
        });

        // Get all academic years
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Build query
        $query = PembayaranUkt::with(['mahasiswa:id,name,nim', 'tahunAjaran:id,tahun,semester']);

        // Apply filters
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran);
        } elseif ($tahunAjaranAktif) {
            $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->whereHas('mahasiswa', function ($q) use ($searchTerm) {
                $q->where('nim', 'like', $searchTerm)
                    ->orWhere('name', 'like', $searchTerm);
            });
        }

        // Get statistics for current filter
        $statistics = [
            'total' => (clone $query)->count(),
            'bayar' => (clone $query)->where('status', 'bayar')->count(),
            'belum_bayar' => (clone $query)->where('status', 'belum_bayar')->count(),
        ];

        // Get paginated data
        $pembayaran = $query->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ukt.index', compact(
            'pembayaran',
            'tahunAjaranList',
            'tahunAjaranAktif',
            'statistics'
        ));
    }

    public function create()
    {
        // Get only students who don't have payment record for active academic year
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();

        $mahasiswa = User::role('mahasiswa')
            ->select('id', 'name', 'nim')
            ->when($tahunAjaranAktif, function ($q) use ($tahunAjaranAktif) {
                $q->whereDoesntHave('pembayaranUkt', function ($subQ) use ($tahunAjaranAktif) {
                    $subQ->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                });
            })
            ->orderBy('nim')
            ->get();

        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.ukt.create', compact('mahasiswa', 'tahunAjaranList', 'tahunAjaranAktif'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mahasiswa_id' => 'required|exists:users,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'status' => 'required|in:bayar,belum_bayar'
        ], [
            'mahasiswa_id.required' => 'Mahasiswa wajib dipilih',
            'mahasiswa_id.exists' => 'Mahasiswa tidak ditemukan',
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak ditemukan',
            'status.required' => 'Status pembayaran wajib dipilih',
            'status.in' => 'Status pembayaran tidak valid',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Validasi gagal, periksa kembali form Anda');
        }

        // Check duplicate
        $exists = PembayaranUkt::where('mahasiswa_id', $request->mahasiswa_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data pembayaran untuk mahasiswa ini pada tahun ajaran tersebut sudah ada');
        }

        try {
            DB::beginTransaction();

            PembayaranUkt::create([
                'mahasiswa_id' => $request->mahasiswa_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'status' => $request->status,
                'updated_by' => Auth::id()
            ]);

            DB::commit();

            return redirect()->route('admin.pembayaran-ukt.index')
                ->with('success', 'Data pembayaran UKT berhasil ditambahkan');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create UKT payment', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal menambahkan data pembayaran: ' . $e->getMessage());
        }
    }

    public function edit(PembayaranUkt $pembayaranUkt)
    {
        $pembayaranUkt->load(['mahasiswa:id,name,nim', 'tahunAjaran:id,tahun,semester']);
        return view('admin.ukt.edit', ['pembayaran' => $pembayaranUkt]);
    }

    public function update(Request $request, PembayaranUkt $pembayaranUkt)
    {
        $request->validate([
            'status' => 'required|in:bayar,belum_bayar'
        ], [
            'status.required' => 'Status pembayaran wajib dipilih',
            'status.in' => 'Status pembayaran tidak valid',
        ]);

        try {
            $pembayaranUkt->update([
                'status' => $request->status,
                'updated_by' => Auth::id()
            ]);

            return redirect()->route('admin.pembayaran-ukt.index')
                ->with('success', 'Status pembayaran berhasil diperbarui');

        } catch (\Exception $e) {
            Log::error('Failed to update UKT payment', ['error' => $e->getMessage()]);

            return redirect()->back()
                ->with('error', 'Gagal memperbarui data pembayaran');
        }
    }

    public function updateStatus(Request $request, PembayaranUkt $pembayaranUkt)
    {
        $request->validate([
            'status' => 'required|in:bayar,belum_bayar'
        ]);

        try {
            $oldStatus = $pembayaranUkt->status;

            $pembayaranUkt->update([
                'status' => $request->status,
                'updated_by' => Auth::id()
            ]);

            $statusLabel = $request->status === 'bayar' ? 'Lunas' : 'Belum Bayar';

            return back()->with('success', "Status pembayaran berhasil diubah menjadi: {$statusLabel}");

        } catch (\Exception $e) {
            Log::error('Failed to update status', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengubah status pembayaran');
        }
    }

    public function destroy(PembayaranUkt $pembayaranUkt)
    {
        try {
            DB::beginTransaction();

            $mahasiswaName = $pembayaranUkt->mahasiswa->name;
            $pembayaranUkt->delete();

            DB::commit();

            return back()->with('success', "Data pembayaran {$mahasiswaName} berhasil dihapus");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete UKT payment', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal menghapus data pembayaran');
        }
    }

    public function importForm()
    {
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();

        return view('admin.ukt.import', compact('tahunAjaranList', 'tahunAjaranAktif'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'file' => 'required|mimes:xlsx,xls|max:2048' // Max 2MB
        ], [
            'tahun_ajaran_id.required' => 'Tahun ajaran wajib dipilih',
            'tahun_ajaran_id.exists' => 'Tahun ajaran tidak valid',
            'file.required' => 'File Excel wajib diupload',
            'file.mimes' => 'File harus berformat .xlsx atau .xls',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);

        DB::beginTransaction();

        try {
            $import = new UktPaymentImport($request->tahun_ajaran_id);
            Excel::import($import, $request->file('file'));

            DB::commit();

            $message = $this->generateImportMessage(
                $import->getRowCount(),
                $import->getSkippedCount(),
                $import->getNonMahasiswaCount()
            );

            return redirect()->route('admin.pembayaran-ukt.index')
                ->with('success', $message);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();

            return back()
                ->with('error', 'Validasi gagal pada beberapa baris')
                ->with('failures', $failures)
                ->withInput();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('UKT import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateImportMessage($imported, $skipped, $nonMahasiswa)
    {
        $messages = [];

        if ($imported > 0) {
            $messages[] = "✓ {$imported} data berhasil diimport";
        }

        if ($skipped > 0) {
            $messages[] = "⚠ {$skipped} data dilewati (sudah ada/tidak valid)";
        }

        if ($nonMahasiswa > 0) {
            $messages[] = "ℹ {$nonMahasiswa} data diabaikan (bukan mahasiswa)";
        }

        return implode('. ', $messages);
    }

    /**
     * Generate safe filename for export
     */
    private function generateSafeFilename($tahunAjaran, $status)
    {
        // Base parts
        $parts = ['pembayaran_ukt'];

        // Add academic year
        if ($tahunAjaran) {
            $tahunPart = $tahunAjaran->tahun . '_' . $tahunAjaran->semester;
            // Remove any special characters
            $tahunPart = preg_replace('/[^A-Za-z0-9_-]/', '', $tahunPart);
            $parts[] = $tahunPart;
        } else {
            $parts[] = 'semua';
        }

        // Add status
        if ($status) {
            $parts[] = ($status === 'bayar' ? 'lunas' : 'belum_bayar');
        } else {
            $parts[] = 'all';
        }

        // Add timestamp
        $parts[] = date('Ymd_His');

        // Join and add extension
        $filename = implode('_', $parts) . '.xlsx';

        // Final sanitization
        $filename = str_replace(
            ['/', '\\', ':', '*', '?', '"', '<', '>', '|', ' '],
            '_',
            $filename
        );

        return $filename;
    }

    public function export(Request $request)
    {
        try {
            // Get filter parameters
            $tahunAjaranId = $request->input('tahun_ajaran');
            $status = $request->input('status');
            $search = $request->input('search');

            // Get academic year for filename
            $tahunAjaran = null;
            if ($tahunAjaranId) {
                $tahunAjaran = TahunAjaran::find($tahunAjaranId);
            }

            // Build clean filename without special characters
            $tahunLabel = $tahunAjaran
                ? $tahunAjaran->tahun . '_' . $tahunAjaran->semester
                : 'semua';

            // Sanitize tahun label (remove any special characters)
            $tahunLabel = preg_replace('/[^A-Za-z0-9_-]/', '_', $tahunLabel);

            $statusLabel = $status
                ? ($status === 'bayar' ? 'lunas' : 'belum_bayar')
                : 'all';

            // Create safe filename
            $fileName = sprintf(
                'pembayaran_ukt_%s_%s_%s.xlsx',
                $tahunLabel,
                $statusLabel,
                date('Ymd_His')
            );

            // Additional sanitization - remove any remaining invalid characters
            $fileName = str_replace(['/', '\\', ':', '*', '?', '"', '<', '>', '|'], '_', $fileName);

            // Log export activity
            Log::info('Exporting UKT payment data', [
                'user' => Auth::id(),
                'user_name' => Auth::user()->name,
                'tahun_ajaran_id' => $tahunAjaranId,
                'status' => $status,
                'search' => $search,
                'filename' => $fileName
            ]);

            // Create export instance
            $export = new UktPaymentExport($tahunAjaranId, $status, $search);

            // Download file with proper headers
            return Excel::download($export, $fileName, \Maatwebsite\Excel\Excel::XLSX, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);

        } catch (\Exception $e) {
            Log::error('UKT Export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => Auth::id(),
                'request' => $request->all()
            ]);

            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            $headers = [
                'NIM' => 'Nomor Induk Mahasiswa (wajib terdaftar)',
                'Status' => 'Isi dengan "bayar" atau "belum_bayar"'
            ];

            $examples = [
                ['CONTOH NIM', 'KETERANGAN'],
                ['20210001', 'bayar'],
                ['20210002', 'belum_bayar'],
                ['', ''],
                ['CATATAN PENTING:'],
                ['1. NIM harus sudah terdaftar sebagai mahasiswa'],
                ['2. Status hanya boleh "bayar" atau "belum_bayar"'],
                ['3. Data duplikat akan di-update otomatis'],
                ['4. Hapus baris contoh sebelum import'],
            ];

            $export = new class ($headers, $examples) implements \Maatwebsite\Excel\Concerns\FromArray {
                private $headers;
                private $examples;

                public function __construct($headers, $examples)
                {
                    $this->headers = $headers;
                    $this->examples = $examples;
                }

                public function array(): array
                {
                    return [
                        array_keys($this->headers),
                        array_values($this->headers),
                        [],
                        ...$this->examples
                    ];
                }
            };

            return Excel::download($export, 'template_import_ukt_' . date('Ymd') . '.xlsx');

        } catch (\Exception $e) {
            Log::error('Template download failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal mengunduh template');
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pembayaran_ukts,id',
            'status' => 'required|in:bayar,belum_bayar'
        ]);

        try {
            DB::beginTransaction();

            $updated = PembayaranUkt::whereIn('id', $request->ids)
                ->update([
                    'status' => $request->status,
                    'updated_by' => Auth::id()
                ]);

            DB::commit();

            $statusLabel = $request->status === 'bayar' ? 'Lunas' : 'Belum Bayar';

            return back()->with('success', "{$updated} data berhasil diubah menjadi: {$statusLabel}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bulk update failed', ['error' => $e->getMessage()]);

            return back()->with('error', 'Gagal mengubah status secara massal');
        }
    }

    public function report(Request $request)
    {
        // Get all academic years with pagination
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(6);

        // Get selected academic year
        $selectedTahunAjaran = null;
        if ($request->filled('tahun_ajaran_id')) {
            $selectedTahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        }

        // Get total students with mahasiswa role
        $totalMahasiswa = User::role('mahasiswa')->count();

        // Build payment query
        $query = PembayaranUkt::with(['mahasiswa', 'tahunAjaran', 'updatedBy']);

        // Apply academic year filter
        if ($selectedTahunAjaran) {
            $query->where('tahun_ajaran_id', $selectedTahunAjaran->id);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $searchTerm = '%' . $request->search . '%';
            $query->whereHas('mahasiswa', function ($q) use ($searchTerm) {
                $q->where('nim', 'like', $searchTerm)
                    ->orWhere('name', 'like', $searchTerm);
            });
        }

        // Get paginated payment data
        $pembayaran = $query->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        // Calculate statistics based on selected academic year
        if ($selectedTahunAjaran) {
            // Statistics for specific academic year
            $sudahBayar = PembayaranUkt::where('tahun_ajaran_id', $selectedTahunAjaran->id)
                ->where('status', 'bayar')
                ->count();

            $belumBayar = PembayaranUkt::where('tahun_ajaran_id', $selectedTahunAjaran->id)
                ->where('status', 'belum_bayar')
                ->count();

            $sudahAdaData = $sudahBayar + $belumBayar;
            $belumAdaData = $totalMahasiswa - $sudahAdaData;
        } else {
            // Overall statistics
            $sudahBayar = PembayaranUkt::where('status', 'bayar')->count();
            $belumBayar = PembayaranUkt::where('status', 'belum_bayar')->count();
            $sudahAdaData = PembayaranUkt::distinct('mahasiswa_id')->count('mahasiswa_id');
            $belumAdaData = $totalMahasiswa - $sudahAdaData;
        }

        // Calculate percentages
        $percentagePaid = $totalMahasiswa > 0
            ? number_format(($sudahBayar / $totalMahasiswa) * 100, 1)
            : 0;

        $percentageUnpaid = $totalMahasiswa > 0
            ? number_format(($belumBayar / $totalMahasiswa) * 100, 1)
            : 0;

        $percentageNoData = $totalMahasiswa > 0
            ? number_format(($belumAdaData / $totalMahasiswa) * 100, 1)
            : 0;

        return view('admin.ukt.report', compact(
            'tahunAjaranList',
            'selectedTahunAjaran',
            'pembayaran',
            'totalMahasiswa',
            'sudahBayar',
            'belumBayar',
            'belumAdaData',
            'percentagePaid',
            'percentageUnpaid',
            'percentageNoData'
        ));
    }

    /**
     * Get summary statistics (AJAX endpoint)
     */
    public function getSummary(Request $request)
    {
        $tahunAjaranId = $request->input('tahun_ajaran_id');

        $query = PembayaranUkt::query();

        if ($tahunAjaranId) {
            $query->where('tahun_ajaran_id', $tahunAjaranId);
        }

        $totalMahasiswa = User::role('mahasiswa')->count();
        $bayar = (clone $query)->where('status', 'bayar')->count();
        $belumBayar = (clone $query)->where('status', 'belum_bayar')->count();
        $sudahAdaData = $bayar + $belumBayar;
        $belumAdaData = $totalMahasiswa - $sudahAdaData;

        $summary = [
            'total_mahasiswa' => $totalMahasiswa,
            'sudah_bayar' => $bayar,
            'belum_bayar' => $belumBayar,
            'belum_ada_data' => $belumAdaData,
            'percentage_paid' => $totalMahasiswa > 0
                ? round(($bayar / $totalMahasiswa) * 100, 1)
                : 0,
            'percentage_unpaid' => $totalMahasiswa > 0
                ? round(($belumBayar / $totalMahasiswa) * 100, 1)
                : 0,
            'percentage_no_data' => $totalMahasiswa > 0
                ? round(($belumAdaData / $totalMahasiswa) * 100, 1)
                : 0,
        ];

        return response()->json($summary);
    }

}