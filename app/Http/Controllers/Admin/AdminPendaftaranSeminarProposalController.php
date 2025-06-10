<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PendaftaranSeminarProposal;

class AdminPendaftaranSeminarProposalController extends Controller
{
    public function index()
    {
        $pendaftaran = PendaftaranSeminarProposal::with('user', 'dosenPembimbing')->latest()->get();
        return view('admin.pendaftaran-seminar-proposal.index', compact('pendaftaran'));
    }

    public function show(PendaftaranSeminarProposal $pendaftaranSeminarProposal)
    {
        return view('admin.pendaftaran-seminar-proposal.show', [
            'pendaftaran' => $pendaftaranSeminarProposal
        ]);
    }
}