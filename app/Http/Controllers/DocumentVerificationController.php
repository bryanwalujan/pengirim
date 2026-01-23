<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Verification\KomisiVerificationController;
use App\Http\Controllers\Verification\SuratVerificationController;
use App\Http\Controllers\Verification\SuratUsulanProposalVerificationController;
use App\Http\Controllers\Verification\SuratUsulanSkripsiVerificationController;
use Illuminate\Support\Facades\Log;

class DocumentVerificationController extends Controller
{
    /**
     * Main verification router
     * Routes to appropriate verification controller based on code prefix
     */
    public function verify($code)
    {
        Log::info('Document verification router', [
            'code' => $code,
            'prefix' => substr($code, 0, 4), // Changed to 4 for SUP-, SUS-
            'ip' => request()->ip(),
        ]);

        // Determine document type based on prefix
        $prefix = substr($code, 0, 3);
        $longPrefix = substr($code, 0, 4); // For SUP-, SUS- (4 chars)

        // Route to appropriate controller
        if ($longPrefix === 'SUP-') {
            // ✅ Surat Usulan Proposal
            $controller = new SuratUsulanProposalVerificationController();
            return $controller->verify($code);
        } elseif ($longPrefix === 'SUS-') {
            // ✅ Surat Usulan Skripsi (Ujian Hasil)
            $controller = new SuratUsulanSkripsiVerificationController();
            return $controller->verify($code);
        } elseif (in_array($prefix, ['KP-', 'KH-'])) {
            // Komisi documents (Proposal or Hasil)
            $controller = new KomisiVerificationController();
            return $controller->verify($code);
        } else {
            // Surat documents (Aktif Kuliah, Ijin Survey, etc.)
            $controller = new SuratVerificationController();
            return $controller->verify($code);
        }
    }
}