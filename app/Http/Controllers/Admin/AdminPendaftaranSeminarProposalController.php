<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSeminarProposal;
use Illuminate\Http\Request;

class AdminPendaftaranSeminarProposalController extends Controller
{
    public function index(Request $request)
    {
        $query = PendaftaranSeminarProposal::with('user', 'dosenPembimbing')->latest();

        // Search functionality
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nim', 'like', '%' . $search . '%');
            });
        }

        // Filter by angkatan
        if ($request->has('angkatan') && $request->angkatan != '') {
            $query->where('angkatan', $request->angkatan);
        }

        $pendaftaran = $query->paginate(10);
        $uniqueAngkatan = PendaftaranSeminarProposal::select('angkatan')
            ->distinct()
            ->orderBy('angkatan', 'desc')
            ->pluck('angkatan');

        return view('admin.pendaftaran-seminar-proposal.index', compact('pendaftaran', 'uniqueAngkatan'));
    }

    public function show(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        // For AJAX requests (modal), return partial view
        if (request()->ajax()) {
            return view('admin.pendaftaran-seminar-proposal.detail-modal', [
                'pendaftaran' => $pendaftaranSeminarProposal
            ]);
        }

        // For regular requests, return full view (if still needed)
        return view('admin.pendaftaran-seminar-proposal.show', [
            'pendaftaran' => $pendaftaranSeminarProposal
        ]);
    }
}