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
                Log::warning('Surat tidak ditemukan atau akses ditolak', [
                    'code' => $code,
                    'user_id' => Auth::id(),
                    'surat_id' => $surat ? $surat->id : null,
                ]);
                return back()->with('error', 'Surat tidak ditemukan atau Anda tidak memiliki akses.');
            }

            // Load relasi dengan eager loading yang benar
            $surat->load([
                'mahasiswa',
                'status' => function ($query) {
                    $query->with(['updatedBy']); // Pastikan updatedBy dimuat
                },
                'trackings' => function ($query) {
                    $query->latest()->with('mahasiswa');
                },
                'penandatangan',
                'penandatanganKaprodi',
            ]);

            // Pastikan entri StatusSurat ada
            $currentStatus = $surat->status ? $surat->status : null;
            if (!$currentStatus || $currentStatus === 'unknown') {
                Log::error('Status tidak valid untuk surat', [
                    'surat_id' => $surat->id,
                    'surat_type' => get_class($surat),
                    'user_id' => Auth::id(),
                    'current_status' => $currentStatus,
                ]);
                try {
                    DB::transaction(function () use ($surat) {
                        StatusSurat::updateOrCreate(
                            [
                                'surat_type' => get_class($surat),
                                'surat_id' => $surat->id,
                            ],
                            [
                                'status' => 'diajukan',
                                'catatan_admin' => 'Status default dibuat sistem', // Tambahkan catatan default
                                'updated_by' => Auth::id(),
                            ]
                        );
                    });
                    $surat->refresh(); // Reload relasi
                    $surat->load('status.updatedBy');
                } catch (\Exception $e) {
                    Log::error('Gagal membuat Status default', [
                        'surat_id' => $surat->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            return view('user.tracking-surat.show', compact('surat'));
        }

        return view('user.tracking-surat.index');
    }

    protected function collectTrackingCodes()
    {
        $codes = [];
        $models = [
            SuratAktifKuliah::class,
            // SuratCutiAkademik::class,
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

        while ($left <= $right) {
            $mid = floor(($left + $right) / 2);

            if ($array[$mid] === $target) {
                return $mid;
            }

            if ($array[$mid] < $target) {
                $left = $mid + 1;
            } else {
                $right = $mid - 1;
            }
        }

        return false;
    }

    protected function findSuratByTrackingCode($code)
    {
        $models = [
            SuratAktifKuliah::class,
            // SuratCutiAkademik::class,
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