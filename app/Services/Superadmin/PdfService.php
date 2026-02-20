<?php

namespace App\Services\Superadmin;

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

        if (!$fotoPath) {
            throw new \Exception('Foto rumah tidak ditemukan');
        }

        $imageData = $this->imageService->convertImageToBase64($fotoPath);

        $data = [
            'dataLapangan' => $dataLapangan,
            'imageSrc' => $imageData['src'],
            'tanggal_cetak' => now()->format('d-m-Y H:i:s')
        ];

        $html = view('superadmin.data-lapangan.partials.foto-rumah-pdf', $data)->render();
        $pdf = app('dompdf.wrapper')->loadHTML($html);
        $pdf->setPaper('A4', 'portrait');

        return $pdf;
    }

    /**
     * Generate PDF filename
     */
    public function generatePdfFilename(string $prefix, string $name): string
    {
        $filename = $prefix . '_' . $name . '_' . now()->format('YmdHis') . '.pdf';
        return preg_replace('/[^A-Za-z0-9_\-.]/', '_', $filename);
    }
}
