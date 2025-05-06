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
            ->orderBy('tanggal_bayar', 'desc')
            ->get();
    }

    /**
     * Define the headings for the Excel sheet
     */
    public function headings(): array
    {
        return [
            'NIM',
            'Nama Mahasiswa',
            'Program Studi',
            'Tahun Ajaran',
            'Semester',
            'Status Pembayaran',
            'Tanggal Bayar',
            'Nominal',
            'Metode Pembayaran',
            'Diverifikasi Oleh',
            'Tanggal Verifikasi'
        ];
    }

    /**
     * Map each payment record to the Excel row
     */
    public function map($payment): array
    {
        return [
            $payment->mahasiswa->nim,
            $payment->mahasiswa->name,
            $payment->mahasiswa->prodi ?? '-',
            $payment->tahunAjaran->tahun,
            ucfirst($payment->tahunAjaran->semester),
            $this->getStatusText($payment->status),
            $payment->tanggal_bayar ? $payment->tanggal_bayar->format('d/m/Y') : '-',
            $payment->nominal ? 'Rp ' . number_format($payment->nominal, 0, ',', '.') : '-',
            $payment->metode_pembayaran ?? '-',
            $payment->verified_by ? $payment->verifier->name : '-',
            $payment->verified_at ? $payment->verified_at->format('d/m/Y H:i') : '-'
        ];
    }

    /**
     * Apply styles to the Excel sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings) as bold
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD9D9D9']
                ]
            ],

            // Center align columns
            'A:K' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ]
            ],

            // Left align for name and program study
            'B:C' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                ]
            ],

            // Format for currency
            'H' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ]
            ]
        ];
    }

    /**
     * Convert status code to readable text
     */
    protected function getStatusText($status)
    {
        $statuses = [
            'lunas' => 'Lunas',
            'belum_lunas' => 'Belum Lunas'
        ];

        return $statuses[$status] ?? $status;
    }
}