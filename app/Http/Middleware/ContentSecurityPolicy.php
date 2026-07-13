<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Content Security Policy (CSP) Middleware
 *
 * Mencegah XSS attacks dengan menambahkan CSP headers.
 * CSP membatasi dari mana konten dapat dimuat,
 * mencegah inline scripts/styles yang bisa exploited.
 */
class ContentSecurityPolicy
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Hanya tambahkan CSP header untuk responses HTML
        if ($this->shouldAddCspHeader($response)) {
            $this->addCspHeaders($response);
        }

        return $response;
    }

    /**
     * Tentukan apakah CSP header harus ditambahkan
     */
    private function shouldAddCspHeader(Response $response): bool
    {
        $contentType = $response->headers->get('Content-Type', '');

        // Hanya untuk HTML responses
        return str_contains($contentType, 'text/html')
            || $response->getContent() !== '';
    }

    /**
     * Tambahkan CSP headers ke response
     */
    private function addCspHeaders(Response $response): void
    {
        $cspDirectives = [
            // Default policy: hanya dari origin yang sama
            "default-src 'self'",
            // Izinkan fonts dari Google dan origin sendiri
            "font-src 'self' data: https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdn.datatables.net",
            // Izinkan scripts dari self, inline allowed untuk compatibility
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdn.tailwindcss.com https://cdn.datatables.net https://code.jquery.com https://cdnjs.cloudflare.com https://cdn.ckeditor.com",
            // Style dari self, inline, dan Google Fonts + DataTables CDN
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdn.datatables.net https://cdnjs.cloudflare.com",
            // Images dari self dan data URIs (untuk inline images)
            // https: sudah mencakup OpenStreetMap tile images (Leaflet)
            "img-src 'self' data: https: blob:",
            // Frames dari self saja
            "frame-src 'self'",
            // Connect (AJAX/fetch) dari self + lord-icon CDN (animasi) + Photon geocoding (server-side via PHP, tapi aman)
            "connect-src 'self' https://cdn.lordicon.com https://nominatim.openstreetmap.org https://photon.komoot.io",
            // Media dari self
            "media-src 'self'",
            // Object dan plugin dari self
            "object-src 'none'",
            // Frame ancestors - cegah clickjacking
            "frame-ancestors 'self'",
            // Form action ke self
            "form-action 'self'",
            // Base URI dibatasi ke self
            "base-uri 'self'",
        ];

        $csp = implode('; ', $cspDirectives).';';

        $response->headers->set('Content-Security-Policy', $csp);

        // X-Content-Security-Policy untuk browser lama (IE)
        $response->headers->set('X-Content-Security-Policy', $csp);

        // X-WebKit-CSP untuk browser lama
        $response->headers->set('X-WebKit-CSP', $csp);
    }
}
