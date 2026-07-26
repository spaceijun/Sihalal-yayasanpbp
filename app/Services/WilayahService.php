<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WilayahService
{
    private const CACHE_TTL = 86400; // 24 hours
    private string $baseUrl = 'https://wilayah.id/api';

    /**
     * Get all provinces
     */
    public function getProvinces(): array
    {
        return Cache::remember('provinces', self::CACHE_TTL, function () {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/provinces.json");

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatRegionList($data['data'] ?? []);
                }
            } catch (\Exception $e) {
                Log::error('WilayahService getProvinces Error', ['message' => $e->getMessage()]);
            }

            return [];
        });
    }

    /**
     * Get regencies by province code
     */
    public function getRegencies(string $provinceCode): array
    {
        $cacheKey = "regencies_{$provinceCode}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($provinceCode) {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/regencies/{$provinceCode}.json");

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatRegionList($data['data'] ?? []);
                }
            } catch (\Exception $e) {
                Log::error('WilayahService getRegencies Error', ['message' => $e->getMessage()]);
            }

            return [];
        });
    }

    /**
     * Get districts by regency code
     */
    public function getDistricts(string $regencyCode): array
    {
        $cacheKey = "districts_{$regencyCode}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($regencyCode) {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/districts/{$regencyCode}.json");

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatRegionList($data['data'] ?? []);
                }
            } catch (\Exception $e) {
                Log::error('WilayahService getDistricts Error', ['message' => $e->getMessage()]);
            }

            return [];
        });
    }

    /**
     * Get villages by district code
     */
    public function getVillages(string $districtCode): array
    {
        $cacheKey = "villages_{$districtCode}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($districtCode) {
            try {
                $response = Http::timeout(30)->get("{$this->baseUrl}/villages/{$districtCode}.json");

                if ($response->successful()) {
                    $data = $response->json();
                    return $this->formatRegionList($data['data'] ?? []);
                }
            } catch (\Exception $e) {
                Log::error('WilayahService getVillages Error', ['message' => $e->getMessage()]);
            }

            return [];
        });
    }

    /**
     * Get postal code + coordinates by village name with disambiguation.
     * Matches by kecamatan AND kabupaten (case-insensitive, tolerant of abbreviations).
     * Falls back to kecamatan-only match if no exact match found.
     *
     * @param  string  $kelurahan  Nama kelurahan/desa
     * @param  string  $kecamatan  Nama kecamatan (untuk disambiguasi)
     * @param  string  $kabupaten  Nama kabupaten/kota (untuk disambiguasi)
     * @return array ['kode_pos' => string|null, 'latitude' => float|null, 'longitude' => float|null, 'found' => bool]
     */
    public function getKodePos(string $kelurahan, string $kecamatan, string $kabupaten): array
    {
        $cacheKey = 'kodepos_' . md5("{$kelurahan}|{$kecamatan}|{$kabupaten}");

        return Cache::remember($cacheKey, 86400 * 30, function () use ($kelurahan, $kecamatan, $kabupaten) {
            try {
                $response = Http::timeout(8)
                    ->get('https://kodepos.vercel.app/search/', [
                        'q' => $kelurahan,
                    ]);

                if ($response->failed() || empty($response->json('data'))) {
                    return ['found' => false, 'kode_pos' => null, 'latitude' => null, 'longitude' => null];
                }

                $results = collect($response->json('data'));

                // Cari yang cocok dengan kecamatan & kabupaten (case-insensitive, toleran singkatan)
                $match = $results->first(function ($item) use ($kecamatan, $kabupaten) {
                    return $this->looseMatch($item['district'] ?? '', $kecamatan)
                        && $this->looseMatch($item['regency'] ?? '', $kabupaten);
                });

                // Fallback: cocokkan kecamatan saja
                if (! $match) {
                    $match = $results->first(fn ($item) => $this->looseMatch($item['district'] ?? '', $kecamatan));
                }

                if ($match && ! empty($match['code'])) {
                    return [
                        'kode_pos'  => (string) $match['code'],
                        'latitude'  => isset($match['latitude']) ? (float) $match['latitude'] : null,
                        'longitude' => isset($match['longitude']) ? (float) $match['longitude'] : null,
                        'found'     => true,
                    ];
                }

                return ['found' => false, 'kode_pos' => null, 'latitude' => null, 'longitude' => null];

            } catch (\Exception $e) {
                Log::error('WilayahService getKodePos Error', ['message' => $e->getMessage()]);
                return ['found' => false, 'kode_pos' => null, 'latitude' => null, 'longitude' => null];
            }
        });
    }

    /**
     * Get kecamatan center coordinates (latitude & longitude) using kodepos.vercel.app API.
     * Searches by kecamatan name and filters by kabupaten.
     * Calculates the average latitude & longitude of villages within the district.
     *
     * @param  string  $kecamatan  Nama kecamatan
     * @param  string  $kabupaten  Nama kabupaten/kota (opsional untuk disambiguasi)
     * @return array ['found' => bool, 'latitude' => float|null, 'longitude' => float|null, 'count_villages' => int]
     */
    public function getKecamatanCoordinates(string $kecamatan, string $kabupaten = ''): array
    {
        $cleanKec = trim(preg_replace('/^(Kecamatan|Kec\.?)\s+/i', '', $kecamatan));
        $cleanKab = trim(preg_replace('/^(Kabupaten|Kota|Kab\.?)\s+/i', '', $kabupaten));

        $cacheKey = 'kec_coord_v2_' . md5("{$cleanKec}|{$cleanKab}");

        return Cache::remember($cacheKey, 86400 * 30, function () use ($cleanKec, $cleanKab) {
            try {
                $response = Http::timeout(8)->get('https://kodepos.vercel.app/search/', [
                    'q' => $cleanKec,
                ]);

                if ($response->failed() || empty($response->json('data'))) {
                    // Fallback search with full string
                    $response = Http::timeout(8)->get('https://kodepos.vercel.app/search/', [
                        'q' => "{$cleanKec} {$cleanKab}",
                    ]);
                }

                if ($response->failed() || empty($response->json('data'))) {
                    return ['found' => false, 'latitude' => null, 'longitude' => null, 'count_villages' => 0];
                }

                $results = collect($response->json('data'));

                // Filter matching district & regency
                $matches = $results->filter(function ($item) use ($cleanKec, $cleanKab) {
                    return $this->looseMatch($item['district'] ?? '', $cleanKec)
                        && (empty($cleanKab) || $this->looseMatch($item['regency'] ?? '', $cleanKab));
                });

                if ($matches->isEmpty()) {
                    // Fallback: match district only
                    $matches = $results->filter(fn ($item) => $this->looseMatch($item['district'] ?? '', $cleanKec));
                }

                if ($matches->isNotEmpty()) {
                    $validCoords = $matches->filter(fn ($i) => ! empty($i['latitude']) && ! empty($i['longitude']));

                    if ($validCoords->isNotEmpty()) {
                        $avgLat = $validCoords->avg('latitude');
                        $avgLng = $validCoords->avg('longitude');

                        return [
                            'found'          => true,
                            'latitude'       => (float) round($avgLat, 7),
                            'longitude'      => (float) round($avgLng, 7),
                            'count_villages' => $validCoords->count(),
                        ];
                    }
                }

                return ['found' => false, 'latitude' => null, 'longitude' => null, 'count_villages' => 0];

            } catch (\Exception $e) {
                Log::error('WilayahService getKecamatanCoordinates Error', ['message' => $e->getMessage()]);
                return ['found' => false, 'latitude' => null, 'longitude' => null, 'count_villages' => 0];
            }
        });
    }

    /**
     * Fuzzy-match dua string wilayah Indonesia.
     * Toleran terhadap: KOTA/KABUPATEN prefix, huruf kapital, strip, spasi ganda.
     */
    private function looseMatch(string $a, string $b): bool
    {
        $normalize = fn (string $s) => strtoupper(
            preg_replace('/\s+/', ' ', trim(
                preg_replace('/\b(KOTA|KABUPATEN|KAB\.?|KEL\.?|KECAMATAN|KEC\.?)\b/i', '', $s)
            ))
        );

        $na = $normalize($a);
        $nb = $normalize($b);

        return $na === $nb
            || str_contains($na, $nb)
            || str_contains($nb, $na);
    }

    /**
     * Format region list to standard format
     */
    private function formatRegionList(array $items): array
    {
        return collect($items)->map(function ($item) {
            return [
                'code' => $item['code'] ?? '',
                'name' => $item['name'] ?? '',
            ];
        })->values()->toArray();
    }

    /**
     * Clear all cached wilayah data
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}
