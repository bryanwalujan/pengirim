<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\PembayaranUkt;
use App\Exports\UktPaymentExport;
use App\Imports\UktPaymentImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
            'file' => 'required|mimes:xlsx,xls',
            'reset_existing' => 'sometimes|boolean'
        ]);

        if ($request->reset_existing) {
            PembayaranUkt::where('tahun_ajaran_id', $request->tahun_ajaran_id)->delete();
        }

        try {
            Excel::import(new UktPaymentImport($request->tahun_ajaran_id), $request->file('file'));

            return redirect()->route('admin.ukt.index')
                ->with('success', 'Data pembayaran UKT berhasil diimport');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function verify(PembayaranUkt $pembayaranUkt)
    {
        $pembayaranUkt->update([
            'status' => 'lunas',
            'verified_by' => User::find(Auth::id()),
            'verified_at' => now(),
            'tanggal_bayar' => $pembayaranUkt->tanggal_bayar ?? now()
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
            'verified_by' => null,
            'verified_at' => null
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
    public function downloadTemplate()
    {
        $path = storage_path('app/public/templates/template_import_ukt.xlsx');

        if (!file_exists($path)) {
            abort(404, 'Template file not found');
        }

        return response()->download($path, 'template_import_pembayaran_ukt.xlsx');
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
