<?php

namespace App\Services\Superadmin;

use App\Models\DataEntryPenagihan;
use App\Models\DataLapangan;
use Barryvdh\DomPDF\PDF;

class PdfService
{
    public function __construct(
        private ImageService $imageService
    ) {}

    /**
     * Generate foto rumah PDF
     */
    public function generateFotoRumahPdf(DataLapangan $dataLapangan): PDF
    {
        $fotoPath = $this->imageService->getFotoRumahPath($dataLapangan);

        if (! $fotoPath) {
            throw new \Exception('Foto rumah tidak ditemukan');
        }

        $imageData = $this->imageService->convertImageToBase64($fotoPath);

        $data = [
            'dataLapangan' => $dataLapangan,
            'imageSrc' => $imageData['src'],
            'tanggal_cetak' => now()->format('d-m-Y H:i:s'),
        ];

        $html = view('superadmin.data-lapangan.partials.foto-rumah-pdf', $data)->render();
        $pdf = app('dompdf.wrapper')->loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate receipt PDF for DataEntryPenagihan.
     * Returns a DomPDF instance ready to stream or save.
     */
    public function generateReceiptPdf(DataEntryPenagihan $penagihan): PDF
    {
        $penagihan->loadMissing('dataEntry.bank');

        $dataEntry = $penagihan->dataEntry;
        $tarifPerPaket = $penagihan->jumlah_paket > 0
            ? (int) round($penagihan->nominal / $penagihan->jumlah_paket)
            : 0;

        $data = [
            'penagihan' => $penagihan,
            'dataEntry' => $dataEntry,
            'tarifPerPaket' => $tarifPerPaket,
            'nominalTerbilang' => self::terbilang((int) $penagihan->nominal),
            'tanggalCetak' => now()->format('d M Y, H:i').' WIB',
        ];

        $html = view('superadmin.penagihan.receipt-pdf', $data)->render();
        $pdf = app('dompdf.wrapper')->loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate PDF filename
     */
    public function generatePdfFilename(string $prefix, string $name): string
    {
        $filename = $prefix.'_'.$name.'_'.now()->format('YmdHis').'.pdf';

        return preg_replace('/[^A-Za-z0-9_\-.]/', '_', $filename);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Terbilang Helper (Bahasa Indonesia)
    // ─────────────────────────────────────────────────────────────────────────

    private static function terbilang(int $angka): string
    {
        $kata = [
            '', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima',
            'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh',
            'Sebelas', 'Dua Belas', 'Tiga Belas', 'Empat Belas', 'Lima Belas',
            'Enam Belas', 'Tujuh Belas', 'Delapan Belas', 'Sembilan Belas',
        ];

        if ($angka < 20) {
            return $kata[$angka];
        }
        if ($angka < 100) {
            return $kata[(int) ($angka / 10) + 10].($angka % 10 ? ' '.$kata[$angka % 10] : '');
        }
        if ($angka < 200) {
            return 'Seratus'.($angka % 100 ? ' '.self::terbilang($angka % 100) : '');
        }
        if ($angka < 1_000) {
            return $kata[(int) ($angka / 100)].' Ratus'.($angka % 100 ? ' '.self::terbilang($angka % 100) : '');
        }
        if ($angka < 2_000) {
            return 'Seribu'.($angka % 1_000 ? ' '.self::terbilang($angka % 1_000) : '');
        }
        if ($angka < 1_000_000) {
            return self::terbilang((int) ($angka / 1_000)).' Ribu'.($angka % 1_000 ? ' '.self::terbilang($angka % 1_000) : '');
        }
        if ($angka < 1_000_000_000) {
            return self::terbilang((int) ($angka / 1_000_000)).' Juta'.($angka % 1_000_000 ? ' '.self::terbilang($angka % 1_000_000) : '');
        }

        return self::terbilang((int) ($angka / 1_000_000_000)).' Miliar'.($angka % 1_000_000_000 ? ' '.self::terbilang($angka % 1_000_000_000) : '');
    }
}
