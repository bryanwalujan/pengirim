<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\StatusSurat;
use App\Models\SuratAktifKuliah;
use App\Models\SuratCutiAkademik;
use App\Models\SuratIjinSurvey;
use App\Models\SuratPindah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackingSuratController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('tracking_code')) {
            $request->validate([
                'tracking_code' => 'required|string|size:12',
            ]);

            $code = $request->tracking_code;

            // Kumpulkan semua tracking code
            $trackingCodes = $this->collectTrackingCodes();
            sort($trackingCodes);

            // Hitung waktu dan iterasi untuk binary search
            $startTime = microtime(true);
            $searchResult = $this->binarySearch($trackingCodes, $code);
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // Konversi ke milidetik
            $iterationCount = is_array($searchResult) ? $searchResult['iterations'] : null;

            // Binary search
            if ($this->binarySearch($trackingCodes, $code) === false) {
                Log::warning('Kode tracking tidak ditemukan', [
                    'code' => $code,
                    'user_id' => Auth::id(),
                ]);
                return back()->with('error', 'Kode tracking tidak ditemukan.');
            }

            // Cari surat
            $surat = $this->findSuratByTrackingCode($code);

            if (!$surat || $surat->mahasiswa_id !== Auth::id()) {
                return back()->with('error', 'Surat tidak ditemukan atau Anda tidak memiliki akses.');
            }

            // Load relasi dengan eager loading yang benar
            $surat->load([
                'mahasiswa',
                'status.updatedBy', // Pastikan updatedBy dimuat
                'trackings.mahasiswa',
                'penandatangan',
                'penandatanganKaprodi',
            ]);

            // Pastikan status ada
            if (!$surat->status()->exists()) {
                StatusSurat::create([
                    'surat_type' => get_class($surat),
                    'surat_id' => $surat->id,
                    'status' => 'diajukan',
                    'catatan_admin' => 'Status default dibuat sistem',
                    'updated_by' => Auth::id(),
                ]);
                $surat->load('status.updatedBy'); // Reload relasi status dan updatedBy
            }


            return view('user.tracking-surat.show', compact('surat', 'executionTime', 'iterationCount'));
        }

        return view('user.tracking-surat.index');
    }

    protected function collectTrackingCodes()
    {
        $codes = [];
        $models = [
            SuratAktifKuliah::class,
            SuratCutiAkademik::class,
            // SuratPindah::class,
            // SuratIjinSurvey::class,
        ];

        foreach ($models as $model) {
            $codes = array_merge($codes, $model::pluck('tracking_code')->filter()->toArray());
        }

        return array_unique($codes);
    }

    protected function binarySearch($array, $target)
    {
        $left = 0;
        $right = count($array) - 1;
        $iterations = 0;

        while ($left <= $right) {
            $iterations++;
            $mid = floor(($left + $right) / 2);

            if ($array[$mid] === $target) {
                return ['index' => $mid, 'iterations' => $iterations];
            }

            if ($array[$mid] < $target) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        return ['index' => false, 'iterations' => $iterations];
    }

    protected function findSuratByTrackingCode($code)
    {
        $models = [
            SuratAktifKuliah::class,
            SuratCutiAkademik::class,
            // SuratPindah::class,
            // SuratIjinSurvey::class,
        ];

        foreach ($models as $model) {
            $surat = $model::where('tracking_code', $code)->first();
            if ($surat) {
                return $surat;
            }
        }

        return null;
    }
}