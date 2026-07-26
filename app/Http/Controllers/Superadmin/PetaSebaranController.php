<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Traits\HasRoutePrefix;
use App\Services\WilayahService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PetaSebaranController extends Controller
{
    use HasRoutePrefix;

    // ─── Emsifa API base URL ───
    const EMSIFA = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    public function __construct(
        private WilayahService $wilayahService
    ) {}

    public function index(): View
    {
        $routePrefix = $this->routePrefix();
        return view('superadmin.peta-sebaran.index', compact('routePrefix'));
    }

    /**
     * Return all records instantly, grouped by Desa/Kelurahan + kode_wilayah.
     * No geocoding here — geocoding is done on-demand per unique Desa/Kelurahan.
     */
    public function data(Request $request): JsonResponse
    {
        $query = DataLapangan::query()
            ->select(['id', 'nama_pu', 'nik', 'alamat', 'kelurahan', 'kecamatan', 'kabupaten',
                      'provinsi', 'status', 'status_pembayaran', 'enumerator_id',
                      'no_registrasi', 'nama_produk', 'created_at'])
            ->with('enumerator:id,nama_lengkap')
            ->whereNotNull('nik')
            ->where('nik', '!=', '');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->get();

        // ── Group by Desa/Kelurahan + 6-digit kode_wilayah ──
        $grouped = [];

        foreach ($records as $item) {
            $nik  = preg_replace('/\D/', '', $item->nik ?? '');
            $kode = strlen($nik) >= 6 ? substr($nik, 0, 6) : null;

            if (!$kode) continue;

            $namaDesa = $this->extractDesaName($item);
            $key      = $kode . '_' . \Illuminate\Support\Str::slug($namaDesa);

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'key'           => $key,
                    'kode'          => $key,
                    'kode_kec'      => $kode,
                    'nama_desa'     => $namaDesa,
                    'kode_prov'     => substr($kode, 0, 2),
                    'kode_kab'      => substr($kode, 0, 4),
                    'lat'           => null,
                    'lng'           => null,
                    'nama_kecamatan'=> null,
                    'nama_kabupaten'=> null,
                    'nama_provinsi' => null,
                    'needs_geocode' => true,
                    'count'         => 0,
                    'items'         => [],
                ];

                // Check cache per desa
                $cached = Cache::get('desa_coord_' . $key);
                if ($cached) {
                    $grouped[$key]['lat']            = (float) $cached['lat'];
                    $grouped[$key]['lng']            = (float) $cached['lng'];
                    $grouped[$key]['nama_desa']      = $cached['nama_desa'] ?? $namaDesa;
                    $grouped[$key]['nama_kecamatan'] = $cached['nama_kecamatan'] ?? null;
                    $grouped[$key]['nama_kabupaten'] = $cached['nama_kabupaten'] ?? null;
                    $grouped[$key]['nama_provinsi']  = $cached['nama_provinsi'] ?? null;
                    $grouped[$key]['needs_geocode']  = false;
                }
            }

            $grouped[$key]['count']++;
            $grouped[$key]['items'][] = [
                'no_registrasi' => $item->no_registrasi ?? '-',
                'nama_pu'       => $item->nama_pu,
                'nama_produk'   => $item->nama_produk ?? '-',
                'status'        => $item->status,
                'pendamping'    => $item->enumerator?->nama_lengkap ?? '-',
                'tanggal'       => $item->created_at?->format('d/m/Y') ?? '-',
            ];
        }

        $clusters = array_values($grouped);

        $uniqueProv = collect($clusters)->pluck('kode_prov')->unique()->filter()->count();
        $uniqueKab  = collect($clusters)->pluck('kode_kab')->unique()->filter()->count();
        $uniqueKec  = collect($clusters)->pluck('kode_kec')->unique()->filter()->count();
        $uniqueDesa = count($clusters);

        return response()->json([
            'success'          => true,
            'total_records'    => $records->count(),
            'total_provinsi'   => $uniqueProv,
            'total_kabupaten'  => $uniqueKab,
            'total_kecamatan'  => $uniqueKec,
            'total_desa'       => $uniqueDesa,
            'data'             => $clusters,
        ]);
    }

    /**
     * Geocode a single Desa/Kelurahan.
     * Uses emsifa API to resolve kecamatan & kabupaten names,
     * then WilayahService (kodepos.vercel.app) for village coordinates.
     * Result cached forever per desa key.
     */
    public function geocodeKecamatan(Request $request): JsonResponse
    {
        $key      = $request->key ?? $request->kode;
        $kodeKec  = $request->kode_kec ?? substr(preg_replace('/\D/', '', $key ?? ''), 0, 6);
        $namaDesa = $request->nama_desa;

        // If key has underscore format "330217_sudimara"
        if (str_contains($key, '_')) {
            $parts    = explode('_', $key, 2);
            $kodeKec  = $parts[0];
            if (!$namaDesa) {
                $namaDesa = ucwords(str_replace('-', ' ', $parts[1]));
            }
        }

        $cacheKey = 'desa_coord_' . $key;

        // Already cached?
        if ($cached = Cache::get($cacheKey)) {
            if ($cached['lat'] && $cached['lng']) {
                return response()->json(['success' => true, 'cached' => true] + $cached);
            }
            return response()->json(['success' => false, 'message' => 'Desa tidak ditemukan (cached).']);
        }

        try {
            // ── Step 1: Resolve kecamatan & kabupaten names from emsifa API ──
            $provCode = substr($kodeKec, 0, 2);
            $kabCode  = substr($kodeKec, 0, 4);

            $provinces  = $this->emsifaProvinces();
            $provName   = collect($provinces)->firstWhere('id', $provCode)['name'] ?? null;

            $regencies  = $this->emsifaRegencies($provCode);
            $kabName    = collect($regencies)->firstWhere('id', $kabCode)['name'] ?? null;

            $districts  = $this->emsifaDistricts($kabCode);
            $district   = collect($districts)->first(
                fn($d) => substr(preg_replace('/\D/', '', $d['id']), 0, 6) === $kodeKec
            );
            $kecName = $district['name'] ?? null;

            // ── Step 2: Query WilayahService (kodepos.vercel.app) for Desa ──
            if ($namaDesa) {
                $desaRes = $this->wilayahService->getDesaCoordinates($namaDesa, $kecName ?? '', $kabName ?? '');
                if ($desaRes['found'] && !empty($desaRes['latitude']) && !empty($desaRes['longitude'])) {
                    $data = [
                        'lat'            => (float) $desaRes['latitude'],
                        'lng'            => (float) $desaRes['longitude'],
                        'nama_desa'      => $desaRes['village'] ?? $namaDesa,
                        'nama_kecamatan' => $kecName ?? $desaRes['district'] ?? null,
                        'nama_kabupaten' => $kabName ?? $desaRes['regency'] ?? null,
                        'nama_provinsi'  => $provName,
                        'source'         => 'kodepos.vercel.app',
                    ];

                    Cache::forever($cacheKey, $data);

                    return response()->json([
                        'success' => true,
                        'cached'  => false,
                    ] + $data);
                }
            }

            // Not found — cache sentinel for 7 days
            Cache::put($cacheKey, ['lat' => null, 'lng' => null], now()->addDays(7));
            return response()->json(['success' => false, 'message' => "Koordinat tidak ditemukan untuk desa {$namaDesa}"]);

        } catch (\Exception $e) {
            Log::warning("Geocode desa [{$key}]: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Ekstrak nama desa/kelurahan dari record DataLapangan
     */
    private function extractDesaName($record): string
    {
        if (! empty($record->kelurahan)) {
            return trim($record->kelurahan);
        }

        $alamat = trim($record->alamat ?? '');
        if (empty($alamat)) {
            return 'Utama';
        }

        // Ekstrak nama desa jika mengandung "DESA XXX"
        if (preg_match('/\bDESA\s+([A-Za-z\s]+)/i', $alamat, $m)) {
            $parts = explode(',', $m[1]);
            return trim($parts[0]);
        }

        // Clean prefix umum
        $cleaned = preg_replace('/\b(DUSUN|DESA|KELURAHAN|KEL\.?|RT\s*\d*|RW\s*\d*|NO\.?\s*\d*|KAMPUNG|DK\.?|JL\.?|JALAN)\b/i', '', $alamat);
        $cleaned = preg_replace('/[^a-zA-Z\s]/', ' ', $cleaned);
        $words   = array_values(array_filter(explode(' ', $cleaned)));

        if (! empty($words)) {
            return mb_convert_case(implode(' ', array_slice($words, 0, 2)), MB_CASE_TITLE, 'UTF-8');
        }

        return 'Utama';
    }

    // ──────────────────────────────────────────
    //  Private helpers
    // ──────────────────────────────────────────

    private function emsifaProvinces(): array
    {
        return Cache::rememberForever('emsifa_provinces', function () {
            $res = Http::timeout(10)->get(self::EMSIFA . '/provinces.json');
            return $res->successful() ? $res->json() : [];
        });
    }

    private function emsifaRegencies(string $provCode): array
    {
        return Cache::rememberForever("emsifa_regencies_{$provCode}", function () use ($provCode) {
            $res = Http::timeout(10)->get(self::EMSIFA . "/regencies/{$provCode}.json");
            return $res->successful() ? $res->json() : [];
        });
    }

    private function emsifaDistricts(string $kabCode): array
    {
        return Cache::rememberForever("emsifa_districts_{$kabCode}", function () use ($kabCode) {
            $res = Http::timeout(10)->get(self::EMSIFA . "/districts/{$kabCode}.json");
            return $res->successful() ? $res->json() : [];
        });
    }

    /** Strip "Kabupaten", "Kota", "Kecamatan" prefix for cleaner Nominatim queries */
    private function cleanWilayahName(?string $name): ?string
    {
        if (!$name) return null;
        return trim(preg_replace('/^(Kabupaten|Kota|Kecamatan)\s+/i', '', $name));
    }
}
