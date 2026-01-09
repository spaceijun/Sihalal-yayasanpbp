<?php

namespace App\Exports;

use App\Models\DataLapangan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\Request;

class DataLapangansExport implements FromCollection, WithHeadings, WithMapping, WithStyles
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
        $query = DataLapangan::with('enumerator');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_pu', 'LIKE', "%{$search}%")
                    ->orWhereHas('enumerator', function ($query) use ($search) {
                        $query->where('name', 'LIKE', "%{$search}%");
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
            'Tanggal Input',
            'Nama PU',
            'Status',
            'Status Pembayaran',
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
            \Carbon\Carbon::parse($dataLapangan->created_at)->format('d/m/Y H:i'),
            $dataLapangan->nama_pu,
            $dataLapangan->status,
            $dataLapangan->status_pembayaran,
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }
}
