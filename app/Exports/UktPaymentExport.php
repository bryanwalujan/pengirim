<?php

namespace App\Exports;

use App\Models\PembayaranUkt;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UktPaymentExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $tahunAjaranId;
    protected $status;

    public function __construct($tahunAjaranId = null, $status = null)
    {
        $this->tahunAjaranId = $tahunAjaranId;
        $this->status = $status;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return PembayaranUkt::with(['mahasiswa', 'tahunAjaran'])
            ->when($this->tahunAjaranId, function ($query) {
                $query->where('tahun_ajaran_id', $this->tahunAjaranId);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->orderBy('status')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NIM',
            'Nama Mahasiswa',
            'Tahun Ajaran',
            'Semester',
            'Status Pembayaran',
            'Terakhir Diupdate'
        ];
    }

    /**
     * Define the headings for the Excel sheet
     */


    /**
     * Map each payment record to the Excel row
     */
    public function map($payment): array
    {
        return [
            $payment->mahasiswa->nim,
            $payment->mahasiswa->name,
            $payment->tahunAjaran->tahun,
            ucfirst($payment->tahunAjaran->semester),
            $this->getStatusText($payment->status),
            $payment->updated_at->format('d/m/Y H:i')
        ];
    }

    /**
     * Apply styles to the Excel sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ]
            ],
            'A:G' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],
            'B:C' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ]
            ]
        ];
    }

    /**
     * Convert status code to readable text
     */
    protected function getStatusText($status)
    {
        return $status === 'bayar' ? 'Sudah Bayar' : 'Belum Bayar';
    }
}