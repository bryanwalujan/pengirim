<?php

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
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;

class PembayaranUktController extends Controller
{

    public function index(Request $request)
    {
        // Ambil tahun ajaran aktif
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();

        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $pembayaran = PembayaranUkt::with(['mahasiswa', 'tahunAjaran'])
            ->when($request->tahun_ajaran, function ($query) use ($request) {
                // Hanya terapkan filter jika tahun_ajaran ada dan tidak kosong
                $query->where('tahun_ajaran_id', $request->tahun_ajaran);
            }, function ($query) use ($tahunAjaranAktif, $request) {
                // Default ke tahun ajaran aktif hanya jika tidak ada filter tahun_ajaran
                if ($tahunAjaranAktif && !$request->tahun_ajaran) {
                    $query->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                }
                // Jika tahun_ajaran kosong atau tidak ada tahun ajaran aktif, tidak perlu filter
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->search, function ($query) use ($request) {
                $query->whereHas('mahasiswa', function ($q) use ($request) {
                    $q->where('nim', 'like', '%' . $request->search . '%')
                        ->orWhere('name', 'like', '%' . $request->search . '%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.ukt.index', compact('pembayaran', 'tahunAjaranList', 'tahunAjaranAktif'));
    }

    public function importForm()
    {
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        return view('admin.ukt.import', compact('tahunAjaranList'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajarans,id',
            'file' => 'required|mimes:xlsx,xls|max:2048'
        ]);

        // Debug: Log sebelum import
        Log::info('Starting UKT import', ['tahun_ajaran_id' => $request->tahun_ajaran_id]);

        DB::beginTransaction();

        try {
            $import = new UktPaymentImport($request->tahun_ajaran_id);
            Excel::import($import, $request->file('file'));

            DB::commit();

            // Debug: Log hasil import
            Log::info('UKT import completed', [
                'imported' => $import->getRowCount(),
                'skipped' => $import->getSkippedCount(),
                'non_mahasiswa' => $import->getNonMahasiswaCount()
            ]);

            return redirect()->route('admin.pembayaran-ukt.index')
                ->with('success', $this->generateImportMessage(
                    $import->getRowCount(),
                    $import->getSkippedCount(),
                    $import->getNonMahasiswaCount()
                ));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('UKT import failed', ['error' => $e->getMessage()]);
            return back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function generateImportMessage($imported, $skipped, $nonMahasiswa)
    {
        $message = "Hasil Import: ";
        $message .= "{$imported} data berhasil diimport. ";

        if ($skipped > 0) {
            $message .= "{$skipped} data dilewati (format tidak valid). ";
        }

        if ($nonMahasiswa > 0) {
            $message .= "{$nonMahasiswa} data diabaikan (bukan mahasiswa).";
        }

        return $message;
    }

    /**
     * Update status pembayaran
     */
    public function updateStatus(Request $request, PembayaranUkt $pembayaranUkt)
    {
        $request->validate([
            'status' => 'required|in:bayar,belum_bayar'
        ]);

        $pembayaranUkt->update([
            'status' => $request->status,
            'updated_by' => Auth::id()
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui');
    }
    public function report(Request $request)
    {
        // Ambil tahun ajaran aktif
        $tahunAjaranAktif = TahunAjaran::where('status_aktif', true)->first();

        // Ambil tahun ajaran yang dipilih jika ada
        $selectedTahunAjaran = $request->tahun_ajaran_id
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : $tahunAjaranAktif;

        // Ambil semua tahun ajaran dengan pagination
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->paginate(6, ['*'], 'tahun_page'); // Paginate 6 item per halaman

        // Query dasar untuk data pembayaran
        $query = PembayaranUkt::with(['mahasiswa', 'tahunAjaran'])
            ->when($request->tahun_ajaran_id, function ($q) use ($request) {
                $q->where('tahun_ajaran_id', $request->tahun_ajaran_id);
            }, function ($q) use ($tahunAjaranAktif) {
                // Default filter tahun ajaran aktif jika tidak ada filter
                if ($tahunAjaranAktif) {
                    $q->where('tahun_ajaran_id', $tahunAjaranAktif->id);
                }
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->search, function ($q) use ($request) {
                $q->whereHas('mahasiswa', function ($q) use ($request) {
                    $q->where('nim', 'like', '%' . $request->search . '%')
                        ->orWhere('name', 'like', '%' . $request->search . '%');
                });
            });

        // Hitung statistik
        $totalMahasiswa = User::role('mahasiswa')->count();
        $sudahBayar = (clone $query)->where('status', 'bayar')->count();
        $belumBayar = (clone $query)->where('status', 'belum_bayar')->count();
        $belumAdaData = $totalMahasiswa - ($sudahBayar + $belumBayar);

        $percentagePaid = $totalMahasiswa > 0 ? round(($sudahBayar / $totalMahasiswa) * 100, 2) : 0;
        $percentageUnpaid = $totalMahasiswa > 0 ? round(($belumBayar / $totalMahasiswa) * 100, 2) : 0;
        $percentageNoData = $totalMahasiswa > 0 ? round(($belumAdaData / $totalMahasiswa) * 100, 2) : 0;

        // Ambil data untuk tabel
        $pembayaran = $query->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.ukt.report', compact(
            'tahunAjaranList',
            'tahunAjaranAktif',
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
     * Export UKT payments data.
     */
    public function export(Request $request)
    {
        $fileName = 'data_pembayaran_ukt_' . date('Ymd_His') . '.xlsx';

        return Excel::download(new UktPaymentExport(
            $request->tahun_ajaran_id,
            $request->status
        ), $fileName);
    }
    /**
     * Download the import template.
     */
    /**
     * Download the UKT payment import template
     */
    public function downloadTemplate()
    {
        $headers = [
            'NIM' => 'Nomor Induk Mahasiswa (harus mahasiswa terdaftar)',
            'Status' => 'Isi dengan "bayar" atau "belum_bayar"'
        ];

        $examples = [
            ['20210001', 'bayar'], // Example of paid student
            ['20210002', 'belum_bayar'] // Example of unpaid student
        ];
        $export = new class ($headers, $examples) implements FromArray {
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
                    [], // Empty row for separation
                    ['CONTOH DATA (Hanya untuk mahasiswa):'],
                    ...$this->examples,
                    ['CATATAN:'],
                    ['- Sistem hanya akan memproses data mahasiswa'],
                    ['- Status harus "bayar" atau "belum_bayar"'],
                    ['- Data akan diupdate jika NIM sudah ada']
                ];
            }
        };

        return Excel::download($export, 'template-import-ukt.xlsx');
    }
    /**
     * Remove the specified payment.
     */
    public function destroy(PembayaranUkt $pembayaranUkt)
    {
        $pembayaranUkt->delete();

        return back()->with('success', 'Data pembayaran berhasil dihapus');
    }
}
