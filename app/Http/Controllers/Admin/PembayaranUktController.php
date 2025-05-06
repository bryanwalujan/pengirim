<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\PembayaranUkt;
use App\Exports\UktPaymentExport;
use App\Imports\UktPaymentImport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromArray;

class PembayaranUktController extends Controller
{

    public function index(Request $request)
    {
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $pembayaran = PembayaranUkt::with(['mahasiswa', 'tahunAjaran'])
            ->when($request->tahun_ajaran_id, function ($query) use ($request) {
                $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
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
            ->paginate(10);

        return view('admin.ukt.index', compact('pembayaran', 'tahunAjaranList'));
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
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'file' => 'required|mimes:xlsx,xls|max:2048',
            'reset_existing' => 'sometimes|boolean'
        ]);

        DB::beginTransaction();

        try {
            if ($request->reset_existing) {
                PembayaranUkt::where('tahun_ajaran_id', $request->tahun_ajaran_id)->delete();
            }

            $import = new UktPaymentImport($request->tahun_ajaran_id);
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getRowCount();
            $skippedCount = $import->getSkippedCount();

            DB::commit();

            $message = "Import berhasil! {$importedCount} data diproses.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} data dilewati (NIM tidak ditemukan).";
            }

            return redirect()->route('admin.pembayaran-ukt.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Gagal mengimpor data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function verify(PembayaranUkt $pembayaranUkt)
    {
        $pembayaranUkt->update([
            'status' => 'bayar',
            'updated_by' => User::find(Auth::id())
        ]);

        return back()->with('success', 'Pembayaran berhasil diverifikasi');
    }

    /**
     * Reject a payment.
     */
    public function reject(PembayaranUkt $pembayaranUkt)
    {
        $pembayaranUkt->update([
            'status' => 'belum_bayar',
            'updated_by' => User::find(Auth::id())
        ]);

        return back()->with('success', 'Pembayaran berhasil ditolak');
    }

    public function resetPayments(TahunAjaran $tahunAjaran)
    {
        PembayaranUkt::where('tahun_ajaran_id', $tahunAjaran->id)
            ->update(['status' => 'belum_bayar']);

        return back()->with('success', 'Status pembayaran berhasil direset');
    }

    public function report(Request $request)
    {
        $tahunAjaranList = TahunAjaran::orderBy('tahun', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        $prodiList = User::whereHas('roles', function ($q) {
            $q->where('name', 'mahasiswa');
        })
            ->distinct()
            ->pluck('prodi')
            ->filter()
            ->toArray();

        $query = PembayaranUkt::with(['mahasiswa', 'tahunAjaran'])
            ->when($request->tahun_ajaran_id, function ($query) use ($request) {
                $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
            })
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->prodi, function ($query) use ($request) {
                $query->whereHas('mahasiswa', function ($q) use ($request) {
                    $q->where('prodi', $request->prodi);
                });
            });

        $totalMahasiswa = User::role('mahasiswa')
            ->when($request->prodi, function ($query) use ($request) {
                $query->where('prodi', $request->prodi);
            })
            ->count();

        $sudahBayar = (clone $query)->where('status', 'lunas')->count();
        $belumBayar = $totalMahasiswa - $sudahBayar;

        $percentagePaid = $totalMahasiswa > 0 ? round(($sudahBayar / $totalMahasiswa) * 100, 2) : 0;
        $percentageUnpaid = $totalMahasiswa > 0 ? round(($belumBayar / $totalMahasiswa) * 100, 2) : 0;

        $pembayaran = $query->orderBy('status')
            ->orderBy('tanggal_bayar', 'desc')
            ->paginate(20);

        return view('admin.ukt.report', compact(
            'tahunAjaranList',
            'prodiList',
            'pembayaran',
            'totalMahasiswa',
            'sudahBayar',
            'belumBayar',
            'percentagePaid',
            'percentageUnpaid'
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
            'NIM' => 'Nomor Induk Mahasiswa (harus sudah terdaftar)',
            'Status' => 'Isi dengan "bayar" atau "belum_bayar"'
        ];

        $examples = [
            ['20210001', 'bayar'],
            ['20210002', 'belum_bayar']
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
                    ['CONTOH DATA:'],
                    ...$this->examples
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
