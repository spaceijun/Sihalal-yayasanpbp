<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\DataLapangan;
use App\Traits\HasRoutePrefix;
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

    public function index(): View
    {
        $routePrefix = $this->routePrefix();
        return view('superadmin.peta-sebaran.index', compact('routePrefix'));
    }

    /**
     * Return all records instantly, grouped by kode_wilayah (from NIK).
     * No geocoding here — geocoding is done on-demand per unique kecamatan.
     */
    public function data(Request $request): JsonResponse
    {
        $query = DataLapangan::query()
            ->select(['id', 'nama_pu', 'nik', 'alamat', 'status',
                      'status_pembayaran', 'enumerator_id', 'no_registrasi',
                      'nama_produk', 'created_at'])
            ->with('enumerator:id,nama_lengkap')
            ->whereNotNull('nik')
            ->where('nik', '!=', '');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $records = $query->get();

        // ── Group by 6-digit kode_wilayah from NIK ──
        $grouped = [];

        foreach ($records as $item) {
            $nik  = preg_replace('/\D/', '', $item->nik ?? '');
            $kode = strlen($nik) >= 6 ? substr($nik, 0, 6) : null;

            if (!$kode) continue;

            if (!isset($grouped[$kode])) {
                $grouped[$kode] = [
                    'kode'          => $kode,
                    'kode_prov'     => substr($kode, 0, 2),
                    'kode_kab'      => substr($kode, 0, 4),
                    // Coordinates (from cache, null if not yet geocoded)
                    'lat'           => null,
                    'lng'           => null,
                    'nama_kecamatan'=> null,
                    'nama_kabupaten'=> null,
                    'nama_provinsi' => null,
                    'needs_geocode' => true,
                    'count'         => 0,
                    'items'         => [],
                ];

                // Check cache
                $cached = Cache::get('kec_coord_' . $kode);
                if ($cached) {
                    $grouped[$kode]['lat']            = (float) $cached['lat'];
                    $grouped[$kode]['lng']            = (float) $cached['lng'];
                    $grouped[$kode]['nama_kecamatan'] = $cached['nama_kecamatan'] ?? null;
                    $grouped[$kode]['nama_kabupaten'] = $cached['nama_kabupaten'] ?? null;
                    $grouped[$kode]['nama_provinsi']  = $cached['nama_provinsi'] ?? null;
                    $grouped[$kode]['needs_geocode']  = false;
                }
            }

            $grouped[$kode]['count']++;
            $grouped[$kode]['items'][] = [
                'no_registrasi' => $item->no_registrasi ?? '-',
                'nama_pu'       => $item->nama_pu,
                'nama_produk'   => $item->nama_produk ?? '-',
                'status'        => $item->status,
                'pendamping'    => $item->enumerator?->nama_lengkap ?? '-',
                'tanggal'       => $item->created_at?->format('d/m/Y') ?? '-',
            ];
        }

        $clusters = array_values($grouped);

        return response()->json([
            'success'          => true,
            'total_records'    => $records->count(),
            'total_kecamatan'  => count($clusters),
            'data'             => $clusters,
        ]);
    }

    /**
     * Geocode a single kecamatan by its 6-digit kode_wilayah.
     * Uses emsifa API to resolve names, then Nominatim for coordinates.
     * Result cached forever per kode.
     */
    public function geocodeKecamatan(Request $request): JsonResponse
    {
        $request->validate(['kode' => 'required|string|size:6|regex:/^\d{6}$/']);

        $kode     = $request->kode;
        $cacheKey = 'kec_coord_' . $kode;

        // Already cached?
        if ($cached = Cache::get($cacheKey)) {
            if ($cached['lat'] && $cached['lng']) {
                return response()->json(['success' => true, 'cached' => true] + $cached);
            }
            return response()->json(['success' => false, 'message' => 'Kecamatan tidak ditemukan (cached).']);
        }

        try {
            // ── Step 1: Resolve names from emsifa API ──
            $provCode = substr($kode, 0, 2);
            $kabCode  = substr($kode, 0, 4);

            $provinces  = $this->emsifaProvinces();
            $provName   = collect($provinces)->firstWhere('id', $provCode)['name'] ?? null;

            $regencies  = $this->emsifaRegencies($provCode);
            $kabName    = collect($regencies)->firstWhere('id', $kabCode)['name'] ?? null;

            $districts  = $this->emsifaDistricts($kabCode);
            // Match by first 6 chars of district id
            $district   = collect($districts)->first(
                fn($d) => substr(preg_replace('/\D/', '', $d['id']), 0, 6) === $kode
            );
            $kecName = $district['name'] ?? null;

            // ── Step 2: Build Photon query ──
            // Photon by Komoot — free, OSM-based, no API key needed
            $cleanKec = $kecName ? $this->cleanWilayahName($kecName) : null;
            $cleanKab = $this->cleanWilayahName($kabName);
            $cleanProv = $this->cleanWilayahName($provName);

            $queryStr = implode(' ', array_filter([$cleanKec, $cleanKab, $cleanProv]));

            $response = Http::withHeaders([
                'User-Agent' => 'SihalalMapApp/1.0',
            ])->timeout(10)->get('https://photon.komoot.io/api/', [
                'q'     => $queryStr,
                'limit' => 1,
            ]);

            if ($response->successful()) {
                $features = $response->json('features') ?? [];
                if (count($features) > 0) {
                    $coords = $features[0]['geometry']['coordinates'];   // [lng, lat]
                    $data   = [
                        'lat'            => $coords[1],
                        'lng'            => $coords[0],
                        'nama_kecamatan' => $kecName,
                        'nama_kabupaten' => $kabName,
                        'nama_provinsi'  => $provName,
                        'query_used'     => $queryStr,
                    ];

                    Cache::forever($cacheKey, $data);

                    return response()->json([
                        'success' => true,
                        'cached'  => false,
                        'lat'     => (float) $data['lat'],
                        'lng'     => (float) $data['lng'],
                    ] + $data);
                }
            }

            // Not found — cache sentinel for 7 days
            Cache::put($cacheKey, ['lat' => null, 'lng' => null], now()->addDays(7));
            return response()->json(['success' => false, 'message' => "Koordinat tidak ditemukan untuk: {$queryStr}"]);

        } catch (\Exception $e) {
            Log::warning("Geocode kecamatan [{$kode}]: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
