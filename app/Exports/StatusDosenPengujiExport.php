<?php

namespace App\Exports;

use App\Models\PendaftaranUjianHasil;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StatusDosenPengujiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $statistics;
    protected $rowNumber = 0;

    public function __construct()
    {
        $this->statistics = PendaftaranUjianHasil::getPengujiStatistics();
    }

    public function collection()
    {
        return collect($this->statistics);
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Dosen',
            'NIP',
            'Email',
            'Beban Aktif',
            'Beban Historis',
            'Total Beban',
        ];
    }

    public function map($statistic): array
    {
        $this->rowNumber++;
        $dosen = $statistic['dosen'];

        return [
            $this->rowNumber,
            $dosen->name,
            $dosen->nip ?? '-',
            $dosen->email,
            $statistic['beban_active'],
            $statistic['beban_replaced'],
            $statistic['total_beban'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(15);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);

        // Style header row
        $sheet->getStyle('A1:G1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Get last row
        $lastRow = $this->rowNumber + 1;

        // Style all data rows
        if ($lastRow > 1) {
            $sheet->getStyle("A2:G{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center align for numeric columns
            $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function title(): string
    {
        return 'Status Dosen Penguji';
    }
}
