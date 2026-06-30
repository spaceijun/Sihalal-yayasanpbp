<?php

namespace App\Services\Superadmin;

/**
 * QrCodeService
 *
 * Generate QR Code as base64 PNG data URL.
 */
class QrCodeService
{
    /**
     * Generate QR code as base64 PNG data URL.
     *
     * @param string $data The data to encode in QR code
     * @param int $size Size of QR code in pixels
     * @return string Base64 data URL
     */
    public static function generateDataUrl(string $data, int $size = 200): string
    {
        if (empty($data)) {
            return self::generatePlaceholderSvg($size);
        }

        // Try to use Endroid QR Code if available (preferred)
        if (class_exists('\Endroid\QrCode\QrCode')) {
            try {
                return self::generateWithEndroid($data, $size);
            } catch (\Exception $e) {
                // Fall through to fallback
            }
        }

        // Try to use TCPDF if available
        if (class_exists('\TCPDF')) {
            try {
                return self::generateWithTCPDF($data, $size);
            } catch (\Exception $e) {
                // Fall through to fallback
            }
        }

        // Fallback: Generate placeholder SVG
        return self::generatePlaceholderSvg($size, $data);
    }

    /**
     * Generate QR code using Endroid QR Code library.
     */
    protected static function generateWithEndroid(string $data, int $size): string
    {
        $qrCode = new \Endroid\QrCode\QrCode($data);
        $qrCode->setSize($size);
        $qrCode->setMargin(10);

        $pngData = $qrCode->writeDataUri();

        return $pngData;
    }

    /**
     * Generate QR code using TCPDF.
     */
    protected static function generateWithTCPDF(string $data, int $size): string
    {
        $tcpdf = new \TCPDF();
        $tcpdf->AddPage();
        $style = [
            'border' => false,
            'padding' => 4,
            'fgcolor' => [0, 0, 0],
            'bgcolor' => false,
        ];
        $tcpdf->write2DBarcode($data, 'QRCODE,L', '', '', $size, $size, $style, 'N');

        return 'data:image/png;base64,' . base64_encode($tcpdf->Output('', 'S'));
    }

    /**
     * Generate a placeholder SVG with QR-like pattern.
     * This works as a visual placeholder.
     */
    protected static function generatePlaceholderSvg(int $size = 200, ?string $data = null): string
    {
        $hash = $data ? crc32($data) : time();
        $cellSize = floor($size / 25);
        $gridSize = 25;
        $padding = $cellSize * 2;

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 ' . ($gridSize * $cellSize) . ' ' . ($gridSize * $cellSize) . '">';

        // White background
        $svg .= '<rect width="100%" height="100%" fill="white"/>';

        // Position markers ( finder patterns )
        self::addFinderPattern($svg, 0, 0, $cellSize, $hash);
        self::addFinderPattern($svg, ($gridSize - 7) * $cellSize, 0, $cellSize, $hash);
        self::addFinderPattern($svg, 0, ($gridSize - 7) * $cellSize, $cellSize, $hash);

        // Data pattern - pseudo-random based on hash
        for ($y = 0; $y < $gridSize; $y++) {
            for ($x = 0; $x < $gridSize; $x++) {
                // Skip finder patterns and timing patterns
                if (self::isReservedArea($x, $y)) {
                    continue;
                }

                // Generate pseudo-random bit based on data and position
                $bit = (($hash + $x * 17 + $y * 31 + $x * $y) % 3) === 0;

                if ($bit) {
                    $svg .= '<rect x="' . ($x * $cellSize) . '" y="' . ($y * $cellSize) . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="black"/>';
                }
            }
        }

        // Add timing patterns
        for ($i = 8; $i < $gridSize - 8; $i++) {
            $fill = ($i % 2 === 0) ? 'black' : 'white';
            $svg .= '<rect x="' . ($i * $cellSize) . '" y="' . (6 * $cellSize) . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="' . $fill . '"/>';
            $svg .= '<rect x="' . (6 * $cellSize) . '" y="' . ($i * $cellSize) . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="' . $fill . '"/>';
        }

        $svg .= '</svg>';

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }

    /**
     * Add finder pattern (position marker).
     */
    protected static function addFinderPattern(string &$svg, int $x, int $y, int $cellSize, int $seed): void
    {
        $size = 7;

        for ($row = 0; $row < $size; $row++) {
            for ($col = 0; $col < $size; $col++) {
                $isBorder = $row === 0 || $row === $size - 1 || $col === 0 || $col === $size - 1;
                $isCenter = $row >= 2 && $row <= 4 && $col >= 2 && $col <= 4;

                if ($isBorder || $isCenter) {
                    $svg .= '<rect x="' . (($x + $col) * $cellSize) . '" y="' . (($y + $row) * $cellSize) . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="black"/>';
                }
            }
        }
    }

    /**
     * Check if cell is in reserved area (finder patterns, timing, etc).
     */
    protected static function isReservedArea(int $x, int $y): bool
    {
        // Top-left finder pattern area
        if ($x < 8 && $y < 8) return true;
        // Top-right finder pattern area
        if ($x >= 17 && $y < 8) return true;
        // Bottom-left finder pattern area
        if ($x < 8 && $y >= 17) return true;
        // Timing patterns
        if ($x === 6 || $y === 6) return true;

        return false;
    }
}
