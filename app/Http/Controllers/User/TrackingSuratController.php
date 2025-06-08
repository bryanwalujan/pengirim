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

            $progressPercentage = $this->calculateProgressPercentage($surat->statusSurat->status);
            $statusSteps = $this->getStatusSteps($surat->statusSurat->status);
            $statusIcons = [
                'diajukan' => 'bi-send',
                'diproses' => 'bi-gear',
                'disetujui_kaprodi' => 'bi-person-check',
                'disetujui' => 'bi-check-circle',
                'siap_diambil' => 'bi-envelope-open',
                'sudah_diambil' => 'bi-check2-all',
                'ditolak' => 'bi-x-circle',
                'unknown' => 'bi-question-circle'
            ];


            return view('user.tracking-surat.show', compact('surat', 'executionTime', 'iterationCount', 'progressPercentage', 'statusSteps', 'statusIcons'));
        }

        return view('user.tracking-surat.index');
    }

    protected function calculateProgressPercentage($status)
    {
        $statusOrder = [
            'diajukan' => 0,
            'diproses' => 1,
            'disetujui_kaprodi' => 2,
            'disetujui' => 3,
            'siap_diambil' => 4,
            'sudah_diambil' => 5,
            'ditolak' => 0 // Jika ditolak, kembali ke awal
        ];

        $currentStep = $statusOrder[$status] ?? 0;
        $totalSteps = count($statusOrder) - 1; // -1 karena dimulai dari 0

        return ($currentStep / $totalSteps) * 100;
    }

    // Tambahkan method untuk status steps
    protected function getStatusSteps($currentStatus)
    {
        $allSteps = [
            [
                'label' => 'Diajukan',
                'status' => 'diajukan',
                'status_class' => '',
                'icon' => 'bi-send'
            ],
            [
                'label' => 'Diproses',
                'status' => 'diproses',
                'status_class' => '',
                'icon' => 'bi-gear'
            ],
            [
                'label' => 'Disetujui Kaprodi',
                'status' => 'disetujui_kaprodi',
                'status_class' => '',
                'icon' => 'bi-person-check'
            ],
            [
                'label' => 'Disetujui',
                'status' => 'disetujui',
                'status_class' => '',
                'icon' => 'bi-check-circle'
            ],
            [
                'label' => 'Siap Diambil',
                'status' => 'siap_diambil',
                'status_class' => '',
                'icon' => 'bi-envelope-open'
            ],
            [
                'label' => 'Sudah Diambil',
                'status' => 'sudah_diambil',
                'status_class' => '',
                'icon' => 'bi-check2-all'
            ]
        ];

        foreach ($allSteps as &$step) {
            if ($step['status'] === $currentStatus) {
                $step['status_class'] = 'active';
            } elseif (
                array_search($step['status'], array_column($allSteps, 'status')) <
                array_search($currentStatus, array_column($allSteps, 'status'))
            ) {
                $step['status_class'] = 'completed';
            }
        }

        return $allSteps;
    }
    protected function collectTrackingCodes()
    {
        $codes = [];
        $models = [
            SuratAktifKuliah::class,
            SuratCutiAkademik::class,
            SuratPindah::class,
            SuratIjinSurvey::class,
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
            SuratPindah::class,
            SuratIjinSurvey::class,
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