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

                if ($match && ! empty($match['postalcode'])) {
                    return [
                        'kode_pos'  => (string) $match['postalcode'],
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
