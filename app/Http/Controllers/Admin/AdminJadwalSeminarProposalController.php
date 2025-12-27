<?php
namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalSeminarProposal;
use Illuminate\Support\Facades\Storage;
use App\Services\PelaksanaanUjianService;
use App\Models\PendaftaranSeminarProposal;
use Illuminate\Support\Facades\Notification;
use App\Notifications\UndanganSeminarProposal;

class AdminJadwalSeminarProposalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'menunggu_jadwal');

        $query = JadwalSeminarProposal::with([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen'
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

    /**
     * ✅ TAMBAHAN: Calendar view untuk jadwal seminar proposal
     */
    public function calendar(Request $request)
    {
        // Get bulan dan tahun dari request, default ke bulan/tahun sekarang
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Validasi bulan dan tahun
        $bulan = (int) $bulan;
        $tahun = (int) $tahun;

        if ($bulan < 1 || $bulan > 12) {
            $bulan = now()->month;
        }

        if ($tahun < 2020 || $tahun > 2030) {
            $tahun = now()->year;
        }

        // Get jadwal untuk bulan yang dipilih (hanya yang sudah dijadwalkan)
        $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $endDate = Carbon::create($tahun, $bulan, 1)->endOfMonth();

        $jadwals = JadwalSeminarProposal::with([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.proposalPembahas.dosen'
        ])
            ->where('status', 'dijadwalkan')
            ->whereBetween('tanggal_ujian', [$startDate, $endDate])
            ->orderBy('tanggal_ujian')
            ->orderBy('waktu_mulai')
            ->get();

        // Stats untuk bulan ini
        $stats = [
            'total_jadwal' => $jadwals->count(),
            'hari_ini' => $jadwals->filter(function ($j) {
                return $j->tanggal_ujian->isToday();
            })->count(),
            'minggu_ini' => $jadwals->filter(function ($j) {
                return $j->tanggal_ujian->isCurrentWeek();
            })->count(),
            'bulan_ini' => $jadwals->count(),
        ];

        return view('admin.jadwal-seminar-proposal.calendar', compact(
            'jadwals',
            'bulan',
            'tahun',
            'stats'
        ));
    }

    public function create(JadwalSeminarProposal $jadwal)
    {
        if ($jadwal->status !== 'menunggu_jadwal') {
            return back()->with('error', 'Jadwal hanya dapat dibuat untuk yang berstatus menunggu penjadwalan.');
        }

        $jadwal->load([
            'pendaftaranSeminarProposal.user',
            'pendaftaranSeminarProposal.dosenPembimbing',
            'pendaftaranSeminarProposal.komisiProposal',
            'pendaftaranSeminarProposal.proposalPembahas.dosen'
        ]);

        return view('admin.jadwal-seminar-proposal.create', compact('jadwal'));
    }

    /**
     * Store OR Update jadwal
     */
    public function store(Request $request, JadwalSeminarProposal $jadwal)
    {
        $validated = $request->validate([
            'tanggal_ujian' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai',
            'ruangan' => 'required|string|max:100',
        ], [
            'tanggal_ujian.required' => 'Tanggal seminar wajib diisi.',
            'tanggal_ujian.after_or_equal' => 'Tanggal seminar tidak boleh di masa lalu.',
            'waktu_mulai.required' => 'Jam mulai wajib diisi.',
            'waktu_selesai.required' => 'Jam selesai wajib diisi.',
            'waktu_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
            'ruangan.required' => 'Ruangan wajib diisi.',
        ]);

        try {
            DB::beginTransaction();

            // Validasi status
            if (!in_array($jadwal->status, ['menunggu_jadwal', 'dijadwalkan'])) {
                return back()
                    ->with('error', 'Jadwal tidak dapat diubah untuk status ' . $jadwal->status)
                    ->withInput();
            }

            if (!$jadwal->hasSkFile()) {
                return back()
                    ->with('error', 'SK Proposal belum diupload oleh mahasiswa.')
                    ->withInput();
            }

            // Validasi durasi minimum
            $jamMulai = Carbon::parse($validated['waktu_mulai']);
            $jamSelesai = Carbon::parse($validated['waktu_selesai']);
            $durasiMenit = $jamMulai->diffInMinutes($jamSelesai);

            if ($durasiMenit < 30) {
                return back()
                    ->with('warning', 'Durasi seminar minimal 30 menit.')
                    ->withInput();
            }

            // Update jadwal
            $jadwal->update([
                'tanggal_ujian' => $validated['tanggal_ujian'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'ruangan' => $validated['ruangan'],
                'status' => 'dijadwalkan',
            ]);

            // Get batch info
            $scheduledCountTotal = JadwalSeminarProposal::getScheduledCountByDate($validated['tanggal_ujian']);
            $scheduledCountSameTime = JadwalSeminarProposal::getScheduledCountByDateTime(
                $validated['tanggal_ujian'],
                $validated['waktu_mulai'],
                $validated['waktu_selesai']
            );

            // Kirim undangan (hanya jika jadwal baru dibuat)
            $isNewSchedule = $jadwal->wasChanged('tanggal_ujian');

            if ($isNewSchedule) {
                $this->kirimUndanganInternal($jadwal);
            }

            DB::commit();

            $tanggalFormatted = Carbon::parse($validated['tanggal_ujian'])->translatedFormat('l, d F Y');
            $jamFormatted = Carbon::parse($validated['waktu_mulai'])->format('H:i') . ' - ' .
                Carbon::parse($validated['waktu_selesai'])->format('H:i');

            $message = "✅ Jadwal seminar proposal berhasil " . ($isNewSchedule ? 'dibuat' : 'diperbarui') . ".<br>";
            $message .= "📅 <strong>{$scheduledCountTotal} mahasiswa</strong> terjadwal pada <strong>{$tanggalFormatted}</strong><br>";
            $message .= "🕐 <strong>{$scheduledCountSameTime} mahasiswa</strong> pada jam <strong>{$jamFormatted}</strong>";

            return redirect()
                ->route('admin.jadwal-seminar-proposal.show', $jadwal)
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error saat menyimpan jadwal sempro', [
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

            // Kumpulkan semua dosen
            if ($pendaftaran->dosenPembimbing) {
                $dosensToNotify->push($pendaftaran->dosenPembimbing);
            }

            foreach ($pendaftaran->proposalPembahas as $pembahas) {
                if ($pembahas->dosen) {
                    $dosensToNotify->push($pembahas->dosen);
                }
            }

            $dosensToNotify = $dosensToNotify->unique('id')->filter(fn($d) => !empty($d->email));

            if ($dosensToNotify->isEmpty()) {
                return back()->with('error', 'Tidak ada dosen dengan email valid untuk dikirim undangan.');
            }

            $berhasilDiqueue = 0;
            $gagalDiqueue = 0;

            foreach ($dosensToNotify as $dosen) {
                try {
                    // ✅ KIRIM LANGSUNG (tidak pakai delay untuk kirim ulang)
                    $dosen->notify(new UndanganSeminarProposal($jadwal, $dosen->name));
                    $berhasilDiqueue++;

                    Log::info('✅ Undangan sempro berhasil diqueue ulang', [
                        'jadwal_id' => $jadwal->id,
                        'dosen_id' => $dosen->id,
                        'dosen_nama' => $dosen->name,
                        'dosen_email' => $dosen->email,
                        'timestamp' => now(),
                    ]);

                } catch (\Exception $e) {
                    $gagalDiqueue++;

                    Log::error('❌ Gagal kirim ulang undangan ke queue', [
                        'jadwal_id' => $jadwal->id,
                        'dosen_id' => $dosen->id,
                        'dosen_nama' => $dosen->name,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            DB::commit();

            // Log summary
            Log::info('📊 Summary pengiriman ulang undangan', [
                'jadwal_id' => $jadwal->id,
                'total_dosen' => $dosensToNotify->count(),
                'berhasil' => $berhasilDiqueue,
                'gagal' => $gagalDiqueue,
                'dikirim_oleh' => Auth::user()->name,
            ]);

            if ($berhasilDiqueue === 0) {
                return back()->with('error', 'Gagal mengirim undangan ke semua dosen. Silakan periksa log untuk detail.');
            }

            $message = "Undangan berhasil dikirim ulang ke {$berhasilDiqueue} dosen melalui email.";

            if ($gagalDiqueue > 0) {
                $message .= " ({$gagalDiqueue} gagal, silakan periksa log)";
                return back()->with('warning', $message);
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Error saat kirim ulang undangan', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Gagal mengirim ulang undangan: ' . $e->getMessage());
        }
    }

    public function destroy(JadwalSeminarProposal $jadwal)
    {
        try {
            DB::beginTransaction();

            if ($jadwal->status === 'selesai') {
                return back()->with('error', 'Jadwal yang sudah selesai tidak dapat dihapus.');
            }

            $mahasiswaNama = $jadwal->pendaftaranSeminarProposal->user->name;
            $mahasiswaNim = $jadwal->pendaftaranSeminarProposal->user->nim;
            $statusSebelumnya = $jadwal->status;
            $jadwalSebelumnya = [
                'tanggal_ujian' => $jadwal->tanggal_ujian,
                'waktu_mulai' => $jadwal->waktu_mulai,
                'waktu_selesai' => $jadwal->waktu_selesai,
                'ruangan' => $jadwal->ruangan,
            ];

            $fileSkDihapus = false;
            if ($jadwal->file_sk_proposal && Storage::disk('public')->exists($jadwal->file_sk_proposal)) {
                try {
                    Storage::disk('public')->delete($jadwal->file_sk_proposal);
                    $fileSkDihapus = true;
                    Log::info('✅ File SK Proposal berhasil dihapus', [
                        'file_path' => $jadwal->file_sk_proposal,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('⚠️ Gagal hapus file SK Proposal', [
                        'file_path' => $jadwal->file_sk_proposal,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $jadwal->update([
                'file_sk_proposal' => null,
                'tanggal_ujian' => null,
                'waktu_mulai' => null,
                'waktu_selesai' => null,
                'ruangan' => null,
                'status' => 'menunggu_sk',
            ]);

            DB::commit();

            Log::info('✅ Jadwal Seminar Proposal berhasil direset', [
                'jadwal_id' => $jadwal->id,
                'mahasiswa_nama' => $mahasiswaNama,
                'mahasiswa_nim' => $mahasiswaNim,
                'status_sebelumnya' => $statusSebelumnya,
                'status_sekarang' => 'menunggu_sk',
                'jadwal_sebelumnya' => $jadwalSebelumnya,
                'file_sk_dihapus' => $fileSkDihapus,
                'reset_by' => Auth::user()->name,
                'reset_at' => now(),
            ]);

            return redirect()
                ->route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_sk'])
                ->with('success', "Jadwal untuk {$mahasiswaNama} ({$mahasiswaNim}) berhasil dihapus. Mahasiswa sekarang dapat mengupload SK baru.");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('❌ Error saat menghapus jadwal seminar proposal', [
                'jadwal_id' => $jadwal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus jadwal: ' . $e->getMessage());
        }
    }

    public function bulkDestroy(Request $request)
    {
        $validated = $request->validate([
            'jadwal_ids' => 'required|array|min:1',
            'jadwal_ids.*' => 'exists:jadwal_seminar_proposals,id',
        ], [
            'jadwal_ids.required' => 'Pilih minimal 1 jadwal untuk dihapus.',
            'jadwal_ids.array' => 'Format data tidak valid.',
            'jadwal_ids.min' => 'Pilih minimal 1 jadwal untuk dihapus.',
            'jadwal_ids.*.exists' => 'Salah satu jadwal tidak ditemukan.',
        ]);

        try {
            DB::beginTransaction();

            $jadwalIds = $validated['jadwal_ids'];

            $jadwals = JadwalSeminarProposal::whereIn('id', $jadwalIds)
                ->with('pendaftaranSeminarProposal.user')
                ->get();

            $jadwalSelesai = $jadwals->where('status', 'selesai');
            if ($jadwalSelesai->count() > 0) {
                return back()->with('error', 'Terdapat ' . $jadwalSelesai->count() . ' jadwal yang sudah selesai dan tidak dapat dihapus.');
            }

            $berhasilDireset = 0;
            $gagalDireset = 0;
            $filesDeleted = 0;

            foreach ($jadwals as $jadwal) {
                try {
                    if ($jadwal->file_sk_proposal && Storage::disk('public')->exists($jadwal->file_sk_proposal)) {
                        Storage::disk('public')->delete($jadwal->file_sk_proposal);
                        $filesDeleted++;
                    }

                    $jadwal->update([
                        'file_sk_proposal' => null,
                        'tanggal_ujian' => null,
                        'waktu_mulai' => null,
                        'waktu_selesai' => null,
                        'ruangan' => null,
                        'status' => 'menunggu_sk',
                    ]);

                    $berhasilDireset++;

                    Log::info('✅ Jadwal direset (bulk)', [
                        'jadwal_id' => $jadwal->id,
                        'mahasiswa' => $jadwal->pendaftaranSeminarProposal->user->name,
                    ]);

                } catch (\Exception $e) {
                    $gagalDireset++;
                    Log::error('Error bulk reset jadwal', [
                        'jadwal_id' => $jadwal->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            Log::info('✅ Bulk reset jadwal selesai', [
                'total_dipilih' => count($jadwalIds),
                'berhasil_direset' => $berhasilDireset,
                'gagal_direset' => $gagalDireset,
                'files_deleted' => $filesDeleted,
                'reset_by' => Auth::user()->name,
            ]);

            $message = "{$berhasilDireset} jadwal berhasil dihapus dan mahasiswa dapat mengupload SK baru.";
            if ($gagalDireset > 0) {
                $message .= " ({$gagalDireset} gagal dihapus)";
            }

            return redirect()
                ->route('admin.jadwal-seminar-proposal.index', ['status' => 'menunggu_sk'])
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error bulk reset jadwal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghapus jadwal.');
        }
    }

    public function getBatchInfo(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'tanggal_ujian' => 'required|date',
                'waktu_mulai' => 'nullable|date_format:H:i',
                'waktu_selesai' => 'nullable|date_format:H:i',
                'ruangan' => 'nullable|string|max:100', // Tetap validate tapi tidak digunakan untuk filtering
            ]);

            $tanggal_ujian = Carbon::parse($validated['tanggal_ujian'])->format('Y-m-d');
            $jamMulai = $validated['waktu_mulai'] ?? null;
            $jamSelesai = $validated['waktu_selesai'] ?? null;

            // ✅ Get total jadwal per hari
            $scheduledCountTotal = JadwalSeminarProposal::whereDate('tanggal_ujian', $tanggal_ujian)
                ->where('status', 'dijadwalkan')
                ->count();

            // ✅ Get jadwal per waktu yang sama (TANPA FILTER RUANGAN)
            $scheduledCountSameTime = 0;
            if ($jamMulai && $jamSelesai) {
                $scheduledCountSameTime = JadwalSeminarProposal::whereDate('tanggal_ujian', $tanggal_ujian)
                    ->where('waktu_mulai', $jamMulai)
                    ->where('waktu_selesai', $jamSelesai)
                    ->where('status', 'dijadwalkan')
                    ->count();
            }

            // ✅ Get detail schedules untuk di-group (GROUP BY TIME SLOT ONLY)
            $schedules = JadwalSeminarProposal::whereDate('tanggal_ujian', $tanggal_ujian)
                ->where('status', 'dijadwalkan')
                ->with('pendaftaranSeminarProposal.user')
                ->orderBy('waktu_mulai')
                ->orderBy('waktu_selesai')
                ->get();

            // ✅ Group schedules by time slot ONLY (tanpa ruangan)
            $schedulesGrouped = $schedules->groupBy(function ($item) {
                return Carbon::parse($item->waktu_mulai)->format('H:i') . ' - ' .
                    Carbon::parse($item->waktu_selesai)->format('H:i');
            })->map(function ($group, $key) {
                return [
                    'slot' => $key,
                    'count' => $group->count(),
                    'mahasiswa' => $group->map(function ($item) {
                        return [
                            'nama' => $item->pendaftaranSeminarProposal->user->name ?? 'N/A',
                            'nim' => $item->pendaftaranSeminarProposal->user->nim ?? 'N/A',
                            'ruangan' => $item->ruangan ?? 'N/A', // Include ruangan for display
                        ];
                    })->toArray(),
                ];
            })->values();

            // Format tanggal_ujian untuk display
            $tanggalFormatted = Carbon::parse($tanggal_ujian)->locale('id')->translatedFormat('l, d F Y');

            // Log untuk debugging
            Log::info('✅ Batch Info Response:', [
                'tanggal_ujian' => $tanggal_ujian,
                'total' => $scheduledCountTotal,
                'same_time' => $scheduledCountSameTime,
                'grouped_count' => $schedulesGrouped->count(),
            ]);

            return response()->json([
                'success' => true,
                'scheduled_count_total' => $scheduledCountTotal,
                'scheduled_count_same_time' => $scheduledCountSameTime,
                'tanggal_formatted' => $tanggalFormatted,
                'schedules_grouped' => $schedulesGrouped,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('❌ Validation Error getBatchInfo:', [
                'errors' => $e->errors(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()),
                'scheduled_count_total' => 0,
                'scheduled_count_same_time' => 0,
                'tanggal_formatted' => '',
                'schedules_grouped' => [],
            ], 422);

        } catch (\Exception $e) {
            Log::error('❌ Error getBatchInfo:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan server: ' . $e->getMessage(),
                'scheduled_count_total' => 0,
                'scheduled_count_same_time' => 0,
                'tanggal_formatted' => '',
                'schedules_grouped' => [],
            ], 500);
        }
    }



    /**
     * Helper method untuk kirim undangan
     */
    private function kirimUndanganInternal(JadwalSeminarProposal $jadwal)
    {
        $pendaftaran = $jadwal->pendaftaranSeminarProposal;
        $dosensToNotify = collect();

        // Kumpulkan dosen
        if ($pendaftaran->dosenPembimbing) {
            $dosensToNotify->push($pendaftaran->dosenPembimbing);
        }

        foreach ($pendaftaran->proposalPembahas as $pembahas) {
            if ($pembahas->dosen) {
                $dosensToNotify->push($pembahas->dosen);
            }
        }

        $dosensToNotify = $dosensToNotify->unique('id')->filter(fn($d) => !empty($d->email));

        foreach ($dosensToNotify as $dosen) {
            try {
                $dosen->notify((new UndanganSeminarProposal($jadwal, $dosen->name))->delay(now()->addSeconds(5)));

                Log::info('✅ Undangan sempro berhasil diqueue', [
                    'jadwal_id' => $jadwal->id,
                    'dosen_id' => $dosen->id,
                ]);
            } catch (\Exception $e) {
                Log::error('❌ Gagal mengirim undangan', [
                    'jadwal_id' => $jadwal->id,
                    'dosen_id' => $dosen->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

}