<?php

namespace App\Services\Integrasi\Wrgroup;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

/**
 * Transport HTTP ke WRGROUP Super Apps. Server-side saja — membawa API Secret.
 * Tidak melempar exception: semua hasil dinormalisasi agar pemanggil bisa
 * memutuskan retry / tolak / henti berdasarkan status HTTP.
 *
 * Porting 1:1 dari swaraningcode-be (app/Services/Wrgroup/WrgroupClient.php) — lihat
 * .agent/workflows/wrgroup-integrasi.md.
 */
class WrgroupClient
{
    public function enabled(): bool
    {
        return (bool) config('wrgroup.enabled');
    }

    public function configured(): bool
    {
        return filled(config('wrgroup.api_url'))
            && filled(config('wrgroup.business_id'))
            && filled(config('wrgroup.api_key'))
            && filled(config('wrgroup.api_secret'));
    }

    /**
     * POST JSON ke {api_url}{path}.
     *
     * @param  array<string, mixed>  $body
     * @return array{status:int, body:?array<string, mixed>, retry_after:?int, error:?string}
     *                                                                                        status 0 = gagal koneksi/timeout (tak ada respons).
     */
    public function post(string $path, array $body): array
    {
        return $this->send('post', $path, $body);
    }

    /**
     * GET {api_url}{path} (hanya baca, mis. /profil). Bentuk hasil sama dengan post().
     *
     * @return array{status:int, body:?array<string, mixed>, retry_after:?int, error:?string}
     */
    public function get(string $path): array
    {
        return $this->send('get', $path);
    }

    /**
     * GET {api_url}{path} yang membalas file mentah (bukan JSON) — dipakai mengunduh PDF surat yang
     * sudah terbit dari modul Surat WRGROUP. Baru ditambahkan untuk modul itu; endpoint outbox lain
     * (invoice/payment/nihil/heartbeat/komisi) semuanya balasan JSON lewat get()/post() di atas.
     *
     * @return array{status:int, body:?string, error:?string}
     */
    public function getBinary(string $path): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.config('wrgroup.api_key').':'.config('wrgroup.api_secret'),
                'X-Business-ID' => (string) config('wrgroup.business_id'),
            ])
                ->timeout((int) config('wrgroup.http_timeout'))
                ->connectTimeout(5)
                ->withOptions(['verify' => (bool) config('wrgroup.http_verify')])
                ->withoutRedirecting()
                ->get(rtrim((string) config('wrgroup.api_url'), '/').$path);
        } catch (ConnectionException $e) {
            return ['status' => 0, 'body' => null, 'error' => 'Koneksi gagal: '.$e->getMessage()];
        }

        return [
            'status' => $response->status(),
            'body' => $response->successful() ? $response->body() : null,
            'error' => $response->successful() ? null : mb_substr(trim((string) $response->body()), 0, 300),
        ];
    }

    /**
     * POST multipart/form-data ke {api_url}{path} — dipakai submisi yang membawa lampiran file
     * (bukti setoran komisi). Bentuk hasil sama dengan post()/get().
     *
     * @param  array<string, mixed>  $fields
     * @param  array<int, array{name: string, contents: mixed, filename: string}>  $files
     * @return array{status:int, body:?array<string, mixed>, retry_after:?int, error:?string}
     */
    public function postMultipart(string $path, array $fields, array $files): array
    {
        try {
            $request = Http::withHeaders([
                'Authorization' => 'Bearer '.config('wrgroup.api_key').':'.config('wrgroup.api_secret'),
                'X-Business-ID' => (string) config('wrgroup.business_id'),
            ])
                ->acceptJson()
                ->timeout((int) config('wrgroup.http_timeout'))
                ->connectTimeout(5)
                ->withOptions(['verify' => (bool) config('wrgroup.http_verify')])
                ->withoutRedirecting();

            foreach ($files as $file) {
                $request = $request->attach($file['name'], $file['contents'], $file['filename']);
            }

            $url = rtrim((string) config('wrgroup.api_url'), '/').$path;
            $response = $request->post($url, $fields);
        } catch (ConnectionException $e) {
            return ['status' => 0, 'body' => null, 'retry_after' => null, 'error' => 'Koneksi gagal: '.$e->getMessage()];
        }

        $json = $response->json();
        $retryAfter = $response->header('Retry-After');

        return [
            'status' => $response->status(),
            'body' => is_array($json) ? $json : null,
            'retry_after' => is_numeric($retryAfter) ? (int) $retryAfter : null,
            'error' => is_array($json) ? null : mb_substr(trim((string) $response->body()), 0, 300),
        ];
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array{status:int, body:?array<string, mixed>, retry_after:?int, error:?string}
     */
    private function send(string $method, string $path, array $body = []): array
    {
        try {
            $request = Http::withHeaders([
                'Authorization' => 'Bearer '.config('wrgroup.api_key').':'.config('wrgroup.api_secret'),
                'X-Business-ID' => (string) config('wrgroup.business_id'),
            ])
                ->acceptJson()
                ->asJson()
                ->timeout((int) config('wrgroup.http_timeout'))
                ->connectTimeout(5)
                ->withOptions(['verify' => (bool) config('wrgroup.http_verify')])
                ->withoutRedirecting(); // redirect http→https pada POST diam-diam mengubahnya jadi GET

            $url = rtrim((string) config('wrgroup.api_url'), '/').$path;
            $response = $method === 'get' ? $request->get($url) : $request->post($url, $body);
        } catch (ConnectionException $e) {
            return ['status' => 0, 'body' => null, 'retry_after' => null, 'error' => 'Koneksi gagal: '.$e->getMessage()];
        }

        $json = $response->json();
        $retryAfter = $response->header('Retry-After');

        return [
            'status' => $response->status(),
            'body' => is_array($json) ? $json : null,
            'retry_after' => is_numeric($retryAfter) ? (int) $retryAfter : null,
            'error' => is_array($json) ? null : mb_substr(trim((string) $response->body()), 0, 300),
        ];
    }
}
