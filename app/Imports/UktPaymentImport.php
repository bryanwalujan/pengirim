<?php
namespace App\Imports;

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\PembayaranUkt;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UktPaymentImport implements ToCollection, WithHeadingRow
{
    protected $tahunAjaranId;
    private $rowCount = 0;
    private $skippedCount = 0;
    protected $nonMahasiswaCount = 0;


    public function __construct($tahunAjaranId)
    {
        $this->tahunAjaranId = $tahunAjaranId;
    }

    public function collection(Collection $rows)
    {
        // Pastikan role mahasiswa ada
        Role::firstOrCreate(['name' => 'mahasiswa']);

        foreach ($rows as $row) {
            try {
                // Validasi row
                if (empty($row['nim'])) {
                    $this->skippedCount++;
                    Log::warning('Skipped row - NIM kosong', ['row' => $row]);
                    continue;
                }

                if (!isset($row['status'])) {
                    $this->skippedCount++;
                    Log::warning('Skipped row - Status kosong', ['row' => $row]);
                    continue;
                }

                $status = strtolower(trim($row['status']));
                if (!in_array($status, ['bayar', 'belum_bayar'])) {
                    $this->skippedCount++;
                    Log::warning('Skipped row - Status tidak valid', ['row' => $row]);
                    continue;
                }

                // Cari mahasiswa dengan scope mahasiswa (lebih reliable)
                $mahasiswa = User::where('nim', trim($row['nim']))
                    ->whereHas('roles', function ($q) {
                        $q->where('name', 'mahasiswa');
                    })
                    ->first();

                if (!$mahasiswa) {
                    $this->nonMahasiswaCount++;
                    Log::warning('Skipped row - NIM tidak ditemukan atau bukan mahasiswa', [
                        'nim' => $row['nim'],
                        'row' => $row
                    ]);
                    continue;
                }

                // Simpan data
                PembayaranUkt::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'tahun_ajaran_id' => $this->tahunAjaranId
                    ],
                    [
                        'status' => $status,
                        'updated_by' => Auth::id() // Langsung gunakan Auth::id() lebih efisien
                    ]
                );

                $this->rowCount++;
                Log::info('Data berhasil diimport', ['nim' => $row['nim'], 'status' => $status]);

            } catch (\Exception $e) {
                $this->skippedCount++;
                Log::error('Import error', [
                    'error' => $e->getMessage(),
                    'row' => $row,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|string',
            'status' => 'required|in:bayar,belum_bayar',
        ];
    }

    public function getRowCount()
    {
        return $this->rowCount;
    }

    // Method to handle the skipped rows
    public function getSkippedCount()
    {
        return $this->skippedCount;
    }

    public function getNonMahasiswaCount()
    {
        return $this->nonMahasiswaCount;
    }
}