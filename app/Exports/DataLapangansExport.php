<?php

namespace App\Exports;

use App\Models\DataLapangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Request;

class DataLapangansExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = DataLapangan::with(['enumerator.koordinator']);

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_pu', 'LIKE', "%{$search}%")
                    ->orWhereHas('enumerator', function ($query) use ($search) {
                        $query->where('nama_lengkap', 'LIKE', "%{$search}%");
                    });
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['tanggal_dari'])) {
            $query->whereDate('created_at', '>=', $this->filters['tanggal_dari']);
        }

        if (!empty($this->filters['tanggal_sampai'])) {
            $query->whereDate('created_at', '<=', $this->filters['tanggal_sampai']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal Diubah',
            'Koordinator',
            'Pendamping',
            'Nama PU',
            'Alamat',
            'Status',
            'Status Pembayaran',
            'Email SiHalal',
        ];
    }

    /**
     * @var DataLapangan $dataLapangan
     */
    public function map($dataLapangan): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            \Carbon\Carbon::parse($dataLapangan->updated_at)->format('d/m/Y H:i'),
            $dataLapangan->enumerator->koordinator->nama_lengkap ?? 'N/A',
            $dataLapangan->enumerator->nama_lengkap ?? 'N/A',
            $dataLapangan->nama_pu,
            $dataLapangan->alamat,
            $dataLapangan->status,
            $dataLapangan->status_pembayaran,
            $dataLapangan->email_sihalal,
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text with background color
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
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

                // Auto-size columns
                foreach (range('A', 'I') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }

                // Set row height for spacing
                $highestRow = $sheet->getHighestRow();
                for ($row = 1; $row <= $highestRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(25);
                }

                // Apply conditional formatting based on status
                for ($row = 2; $row <= $highestRow; $row++) {
                    $status = $sheet->getCell('G' . $row)->getValue();
                    $statusPembayaran = $sheet->getCell('H' . $row)->getValue();

                    // Styling untuk kolom Status (kolom G)
                    $statusColor = $this->getStatusColor($status);
                    if ($statusColor) {
                        $sheet->getStyle('G' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $statusColor]
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => $this->getTextColor($status)]
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }

                    // Styling untuk kolom Status Pembayaran (kolom H)
                    $pembayaranColor = $this->getStatusPembayaranColor($statusPembayaran);
                    if ($pembayaranColor) {
                        $sheet->getStyle('H' . $row)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $pembayaranColor]
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => $this->getTextColorPembayaran($statusPembayaran)]
                            ],
                            'alignment' => [
                                'horizontal' => Alignment::HORIZONTAL_CENTER,
                                'vertical' => Alignment::VERTICAL_CENTER,
                            ],
                        ]);
                    }

                    // Center alignment untuk semua sel di row
                    $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // Add borders to all cells
                $sheet->getStyle('A1:I' . $highestRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);
            },
        ];
    }

    /**
     * Get color for status field
     */
    private function getStatusColor($status)
    {
        $colors = [
            'PENDING' => 'FFFF00',           // Kuning
            'DITOLAK' => '000000',           // Hitam
            'PROGRESS OSS' => 'ADD8E6',      // Biru muda
            'PROGRESS SIHALAL' => '00008B',  // Biru tua
            'TERBIT SH' => '00FF00',         // Hijau
            'REVISI' => 'FF0000',             // Merah
        ];

        return $colors[$status] ?? null;
    }

    /**
     * Get text color for status field
     */
    private function getTextColor($status)
    {
        // Dark text for light backgrounds, white text for dark backgrounds
        $darkTextStatuses = ['PENDING', 'PROGRESS OSS', 'TERBIT SH'];

        return in_array($status, $darkTextStatuses) ? '000000' : 'FFFFFF';
    }

    /**
     * Get color for status pembayaran field
     */
    private function getStatusPembayaranColor($statusPembayaran)
    {
        $colors = [
            'PENDING' => 'FFFF00',      // Kuning
            'PENGAJUAN' => 'ADD8E6',    // Biru muda
            'DIBAYAR' => '00FF00',      // Hijau
        ];

        return $colors[$statusPembayaran] ?? null;
    }

    /**
     * Get text color for status pembayaran field
     */
    private function getTextColorPembayaran($statusPembayaran)
    {
        // Dark text for light backgrounds
        return '000000';
    }
}
