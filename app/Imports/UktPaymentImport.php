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

    public function __construct($tahunAjaranId)
    {
        $this->tahunAjaranId = $tahunAjaranId;
    }

    public function model(array $row)
    {
        $mahasiswa = User::where('nim', $row['nim'])->first();

        if (!$mahasiswa) {
            return null;
        }

        return new PembayaranUkt([
            'mahasiswa_id' => $mahasiswa->id,
            'tahun_ajaran_id' => $this->tahunAjaranId,
            'status' => strtolower($row['status']) === 'bayar' ? 'bayar' : 'belum_bayar',
            'updated_by' => User::find(Auth::id())
        ]);
    }
}