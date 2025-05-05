<?php

namespace App\Http\Controllers\Admin;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use App\Models\PembayaranUkt;
use App\Imports\UktPaymentImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class PembayaranUktController extends Controller
{
    public function importForm()
    {
        $activeYear = TahunAjaran::aktif()->first();
        return view('admin.ukt.import', compact('activeYear'));
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

            return redirect()->route('admin.pembayaran-ukt.index')
                ->with('success', 'Data pembayaran UKT berhasil diimport');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function resetPayments(TahunAjaran $tahunAjaran)
    {
        PembayaranUkt::where('tahun_ajaran_id', $tahunAjaran->id)
            ->update(['status' => 'unpaid']);

        return back()->with('success', 'Status pembayaran berhasil direset');
    }
}
