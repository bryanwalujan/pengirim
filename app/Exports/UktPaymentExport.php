<?php
// filepath: app/Exports/UktPaymentExport.php

namespace App\Exports;

use App\Models\User;
use App\Models\PembayaranUkt;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UktPaymentExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    protected $tahunAjaranId;
    protected $status;
    protected $search;
    protected $rowNumber = 0;
    protected $tahunAjaran;
    protected $statistics;

    public function __construct($tahunAjaranId = null, $status = null, $search = null)
    {
        $this->tahunAjaranId = $tahunAjaranId;
        $this->status = $status;
        $this->search = $search;

        // Get academic year info
        if ($tahunAjaranId) {
            $this->tahunAjaran = TahunAjaran::find($tahunAjaranId);
        }

        // Calculate statistics
        $this->calculateStatistics();
    }

    /**
     * Calculate statistics for summary
     */
    private function calculateStatistics()
    {
        $query = PembayaranUkt::query();

        if ($this->tahunAjaranId) {
            $query->where('tahun_ajaran_id', $this->tahunAjaranId);
        }

        $total = $query->count();
        $bayar = (clone $query)->where('status', 'bayar')->count();
        $belumBayar = (clone $query)->where('status', 'belum_bayar')->count();
        $totalMahasiswa = User::role('mahasiswa')->count();

        $this->statistics = [
            'total' => $total,
            'bayar' => $bayar,
            'belum_bayar' => $belumBayar,
            'total_mahasiswa' => $totalMahasiswa,
            'percentage_bayar' => $total > 0 ? round(($bayar / $total) * 100, 2) : 0,
            'percentage_belum_bayar' => $total > 0 ? round(($belumBayar / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get collection of data
     */
    public function collection()
    {
        $query = PembayaranUkt::with(['mahasiswa:id,name,nim', 'tahunAjaran:id,tahun,semester', 'updatedBy:id,name']);

        // Apply filters
        if ($this->tahunAjaranId) {
            $query->where('tahun_ajaran_id', $this->tahunAjaranId);
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $searchTerm = '%' . $this->search . '%';
            $query->whereHas('mahasiswa', function ($q) use ($searchTerm) {
                $q->where('nim', 'like', $searchTerm)
                    ->orWhere('name', 'like', $searchTerm);
            });
        }

        return $query->latest('updated_at')->get();
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            ['LAPORAN PEMBAYARAN UKT'],
            ['Tahun Ajaran: ' . ($this->tahunAjaran ? $this->tahunAjaran->tahun . ' - ' . ucfirst($this->tahunAjaran->semester) : 'Semua')],
            ['Tanggal Export: ' . now()->isoFormat('DD MMMM YYYY HH:mm')],
            [''],
            ['RINGKASAN DATA'],
            ['Total Data', $this->statistics['total']],
            ['Sudah Bayar', $this->statistics['bayar'] . ' (' . $this->statistics['percentage_bayar'] . '%)'],
            ['Belum Bayar', $this->statistics['belum_bayar'] . ' (' . $this->statistics['percentage_belum_bayar'] . '%)'],
            ['Total Mahasiswa', $this->statistics['total_mahasiswa']],
            [''],
            ['DATA PEMBAYARAN'],
            ['No', 'NIM', 'Nama Mahasiswa', 'Tahun Ajaran', 'Semester', 'Status', 'Terakhir Update', 'Diupdate Oleh']
        ];
    }

    /**
     * Map data rows
     */
    public function map($pembayaran): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $pembayaran->mahasiswa->nim ?? '-',
            $pembayaran->mahasiswa->name ?? '-',
            $pembayaran->tahunAjaran->tahun ?? '-',
            $pembayaran->tahunAjaran ? ucfirst($pembayaran->tahunAjaran->semester) : '-',
            $pembayaran->status === 'bayar' ? 'Lunas' : 'Belum Bayar',
            $pembayaran->updated_at->format('d/m/Y H:i'),
            $pembayaran->updatedBy->name ?? '-'
        ];
    }

    /**
     * Apply styles
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Title
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667EEA']
                ]
            ],

            // Info rows (2-3)
            2 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ]
            ],
            3 => [
                'font' => ['size' => 10, 'italic' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E9ECEF']
                ]
            ],

            // Summary section header (5)
            5 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D1E7DD']
                ]
            ],

            // Summary data (6-9)
            '6:9' => [
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8F9FA']
                ]
            ],

            // Data section header (11)
            11 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FFF3CD']
                ]
            ],

            // Table header (12)
            12 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '667EEA']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
        ];
    }

    /**
     * Register events
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge title cells
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');
                $sheet->mergeCells('A5:H5');
                $sheet->mergeCells('A11:H11');

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(11)->setRowHeight(25);
                $sheet->getRowDimension(12)->setRowHeight(25);

                // Apply borders to data table
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A12:H' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'DDDDDD']
                        ]
                    ]
                ]);

                // Center align for specific columns
                $sheet->getStyle('A13:A' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B13:B' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('E13:E' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F13:F' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Wrap text for long names
                $sheet->getStyle('C13:C' . $lastRow)->getAlignment()
                    ->setWrapText(true);

                // Add alternating row colors for data
                for ($i = 13; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A' . $i . ':H' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8F9FA']
                            ]
                        ]);
                    }
                }

                // Freeze panes at row 13 (after header)
                $sheet->freezePane('A13');
            }
        ];
    }

    /**
     * Set sheet title
     */
    public function title(): string
    {
        return 'Data Pembayaran UKT';
    }
}