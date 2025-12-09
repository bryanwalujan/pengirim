<?php
// filepath: /c:/laragon/www/eservice-app/app/Http/Controllers/Admin/AdminJadwalSeminarProposalController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JadwalSeminarProposal;
use App\Models\PendaftaranSeminarProposal;
use App\Models\User;
use App\Notifications\UndanganSeminarProposal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Carbon\Carbon;

class AdminJadwalSeminarProposalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'menunggu_jadwal');

        // ✅ PERBAIKAN: Hapus .pembimbing1 dan .pembimbing2 dari eager loading
        $query = JadwalSeminarProposal::with([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing', // ✅ Pembimbing utama (hanya 1)
            'pendaftaranSeminarProposal.komisiProposal',   // ✅ Load komisi tanpa nested relations
            'pendaftaranSeminarProposal.proposalPembahas.dosen' // ✅ 3 Pembahas
        ]);

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        $query->latest('updated_at');

        $jadwals = $query->paginate(20)->withQueryString();

        $stats = [
            'menunggu_sk' => JadwalSeminarProposal::menungguSk()->count(),
            'menunggu_jadwal' => JadwalSeminarProposal::menungguJadwal()->count(),
            'dijadwalkan' => JadwalSeminarProposal::dijadwalkan()->count(),
            'selesai' => JadwalSeminarProposal::selesai()->count(),
        ];

        return view('admin.jadwal-seminar-proposal.index', compact('jadwals', 'stats', 'status'));
    }

    public function create(JadwalSeminarProposal $jadwal)
    {
        if ($jadwal->status !== 'menunggu_jadwal') {
            return back()->with('error', 'Jadwal hanya dapat dibuat untuk yang berstatus menunggu penjadwalan.');
        }

        // ✅ PERBAIKAN: Load tanpa nested pembimbing
        $jadwal->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen'
        ]);

        return view('admin.jadwal-seminar-proposal.create', compact('jadwal'));
    }

    public function store(Request $request, JadwalSeminarProposal $jadwal)
    {
        $validated = $request->validate([
            'tanggal' => 'required|date|after_or_equal:today',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'required|string|max:100',
        ], [
            'tanggal.required' => 'Tanggal seminar wajib diisi.',
            'tanggal.date' => 'Format tanggal tidak valid.',
            'tanggal.after_or_equal' => 'Tanggal seminar tidak boleh di masa lalu.',
            'jam_mulai.required' => 'Jam mulai wajib diisi.',
            'jam_mulai.date_format' => 'Format jam mulai harus HH:MM (contoh: 09:00).',
            'jam_selesai.required' => 'Jam selesai wajib diisi.',
            'jam_selesai.date_format' => 'Format jam selesai harus HH:MM (contoh: 11:00).',
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'ruangan.required' => 'Ruangan wajib diisi.',
            'ruangan.max' => 'Nama ruangan maksimal 100 karakter.',
        ]);

        try {
            DB::beginTransaction();

            if ($jadwal->status !== 'menunggu_jadwal') {
                return back()
                    ->with('error', 'Jadwal hanya dapat dibuat untuk yang berstatus menunggu penjadwalan.')
                    ->withInput();
            }

            if (!$jadwal->hasSkFile()) {
                return back()
                    ->with('error', 'SK Proposal belum diupload oleh mahasiswa.')
                    ->withInput();
            }

            // Cek bentrok
            $bentrokRuangan = JadwalSeminarProposal::where('id', '!=', $jadwal->id)
                ->where('tanggal', $validated['tanggal'])
                ->where('ruangan', $validated['ruangan'])
                ->where('status', 'dijadwalkan')
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('jam_mulai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhereBetween('jam_selesai', [$validated['jam_mulai'], $validated['jam_selesai']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('jam_mulai', '<=', $validated['jam_mulai'])
                                ->where('jam_selesai', '>=', $validated['jam_selesai']);
                        });
                })
                ->exists();

            if ($bentrokRuangan) {
                return back()
                    ->with('warning', 'Ruangan sudah terpakai pada tanggal dan jam tersebut.')
                    ->withInput();
            }

            // Update jadwal
            $jadwal->update([
                'tanggal' => $validated['tanggal'],
                'jam_mulai' => $validated['jam_mulai'],
                'jam_selesai' => $validated['jam_selesai'],
                'ruangan' => $validated['ruangan'],
                'status' => 'dijadwalkan',
            ]);

            // ✅ PERBAIKAN: Kumpulkan dosen TANPA pembimbing 2
            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $dosensToNotify = collect();

            // 1. Dosen Pembimbing Utama (dari pendaftaran sempro)
            if ($pendaftaran->dosenPembimbing) {
                $dosensToNotify->push($pendaftaran->dosenPembimbing);
                Log::info('✅ Pembimbing utama ditambahkan', [
                    'dosen_id' => $pendaftaran->dosenPembimbing->id,
                    'dosen_name' => $pendaftaran->dosenPembimbing->name,
                ]);
            }

            // ❌ HAPUS: Tidak ada pembimbing 1 & 2 dari komisi proposal
            // Komisi Proposal hanya punya 1 pembimbing (sama dengan pembimbing utama di atas)

            // 2. Dosen Pembahas 1, 2, 3 (dari proposal pembahas)
            $pembahasList = $pendaftaran->proposalPembahas;
            foreach ($pembahasList as $pembahas) {
                if ($pembahas->dosen) {
                    $dosensToNotify->push($pembahas->dosen);
                    Log::info('✅ Pembahas ditambahkan', [
                        'posisi' => $pembahas->posisi,
                        'dosen_id' => $pembahas->dosen->id,
                        'dosen_name' => $pembahas->dosen->name,
                    ]);
                }
            }

            // Hapus duplikat
            $dosensToNotify = $dosensToNotify->unique('id');

            Log::info('📋 Total dosen yang akan menerima undangan', [
                'total' => $dosensToNotify->count(),
                'dosen_list' => $dosensToNotify->pluck('name', 'id')->toArray(),
            ]);

            // Filter yang punya email
            $dosensWithEmail = $dosensToNotify->filter(function ($dosen) {
                return !empty($dosen->email);
            });

            if ($dosensWithEmail->isEmpty()) {
                DB::commit();
                return redirect()->route('admin.jadwal-seminar-proposal.index', ['status' => 'dijadwalkan'])
                    ->with('warning', 'Jadwal berhasil dibuat, tetapi tidak ada dosen dengan email yang valid.');
            }

            // Kirim notifikasi
            $berhasilDikirim = 0;
            $gagalDikirim = 0;

            foreach ($dosensWithEmail as $dosen) {
                try {
                    $dosen->notify(new UndanganSeminarProposal($jadwal, $dosen->name));
                    $berhasilDikirim++;

                    Log::info('✅ Undangan sempro terkirim', [
                        'jadwal_id' => $jadwal->id,
                        'dosen_id' => $dosen->id,
                        'dosen_nama' => $dosen->name,
                        'dosen_email' => $dosen->email,
                    ]);

                } catch (\Exception $e) {
                    $gagalDikirim++;

                    Log::error('❌ Gagal mengirim undangan sempro', [
                        'jadwal_id' => $jadwal->id,
                        'dosen_id' => $dosen->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            $message = "Jadwal seminar proposal berhasil dibuat. Undangan terkirim ke {$berhasilDikirim} dosen (1 Pembimbing + 3 Pembahas).";

            if ($gagalDikirim > 0) {
                $message .= " ({$gagalDikirim} gagal terkirim)";
            }

            return redirect()->route('admin.jadwal-seminar-proposal.index', ['status' => 'dijadwalkan'])
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saat membuat jadwal sempro', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(JadwalSeminarProposal $jadwal)
    {
        // ✅ PERBAIKAN: Load tanpa nested pembimbing
        $jadwal->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen'
        ]);

        return view('admin.jadwal-seminar-proposal.show', compact('jadwal'));
    }

    public function edit(JadwalSeminarProposal $jadwal)
    {
        if (!in_array($jadwal->status, ['menunggu_jadwal', 'dijadwalkan'])) {
            return back()->with('error', 'Jadwal tidak dapat diubah untuk status ' . $jadwal->status);
        }

        $jadwal->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen'
        ]);

        return view('admin.jadwal-seminar-proposal.edit', compact('jadwal'));
    }

    public function update(Request $request, JadwalSeminarProposal $jadwal)
    {
        return $this->store($request, $jadwal);
    }

    public function downloadSk(JadwalSeminarProposal $jadwal)
    {
        if (!$jadwal->hasSkFile()) {
            return back()->with('error', 'File SK Proposal tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $jadwal->file_sk_proposal);
        $fileName = 'SK_Proposal_' . $jadwal->pendaftaranSeminarProposal->user->nim . '.pdf';

        return response()->download($filePath, $fileName);
    }

    public function viewSk(JadwalSeminarProposal $jadwal)
    {
        if (!$jadwal->hasSkFile()) {
            abort(404, 'File SK Proposal tidak ditemukan.');
        }

        $filePath = storage_path('app/public/' . $jadwal->file_sk_proposal);

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SK_Proposal_' . $jadwal->pendaftaranSeminarProposal->user->nim . '.pdf"'
        ]);
    }

    public function markAsSelesai(JadwalSeminarProposal $jadwal)
    {
        if ($jadwal->status !== 'dijadwalkan') {
            return back()->with('error', 'Hanya jadwal yang sudah dijadwalkan yang dapat ditandai selesai.');
        }

        try {
            DB::beginTransaction();
            $jadwal->update(['status' => 'selesai']);
            DB::commit();

            return back()->with('success', 'Jadwal seminar proposal berhasil ditandai sebagai selesai.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function kirimUlangUndangan(JadwalSeminarProposal $jadwal)
    {
        if ($jadwal->status !== 'dijadwalkan') {
            return back()->with('error', 'Undangan hanya dapat dikirim ulang untuk jadwal yang sudah dijadwalkan.');
        }

        try {
            DB::beginTransaction();

            $pendaftaran = $jadwal->pendaftaranSeminarProposal;
            $dosensToNotify = collect();

            // ✅ PERBAIKAN: Hanya pembimbing utama + 3 pembahas
            if ($pendaftaran->dosenPembimbing) {
                $dosensToNotify->push($pendaftaran->dosenPembimbing);
            }

            // ❌ HAPUS: Tidak ada load pembimbing dari komisi

            foreach ($pendaftaran->proposalPembahas as $pembahas) {
                if ($pembahas->dosen) {
                    $dosensToNotify->push($pembahas->dosen);
                }
            }

            $dosensToNotify = $dosensToNotify->unique('id')->filter(fn($d) => !empty($d->email));

            $berhasilDikirim = 0;
            foreach ($dosensToNotify as $dosen) {
                try {
                    $dosen->notify(new UndanganSeminarProposal($jadwal, $dosen->name));
                    $berhasilDikirim++;
                } catch (\Exception $e) {
                    Log::error('Gagal kirim ulang undangan', [
                        'jadwal_id' => $jadwal->id,
                        'dosen_id' => $dosen->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', "Undangan berhasil dikirim ulang ke {$berhasilDikirim} dosen.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengirim ulang undangan: ' . $e->getMessage());
        }
    }
}