<?php
namespace App\Imports;

use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\PembayaranUkt;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UktPaymentImport implements ToModel, WithHeadingRow
{
    protected $tahunAjaranId;
    private $rowCount = 0;
    private $skippedCount = 0;


    public function __construct($tahunAjaranId)
    {
        $this->tahunAjaranId = $tahunAjaranId;
    }

    public function model(array $row)
    {
        $this->rowCount++;
        $mahasiswa = User::where('nim', $row['nim'])->first();

        if (!$mahasiswa) {
            $this->skippedCount++;
            return null;
        }

        return new PembayaranUkt([
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_ajaran_id' => $this->tahunAjaranId,
            'status' => strtolower($row['status']) === 'bayar' ? 'bayar' : 'belum_bayar',
            'updated_by' => User::find(Auth::id())
        ]);
    }

    public function rules(): array
    {
        return [
            'nim' => 'required|string',
            'status' => 'required|in:lunas,belum_lunas',
            'tanggal_bayar' => 'nullable|date_format:d/m/Y'
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
}