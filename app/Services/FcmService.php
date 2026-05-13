<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    // ── Konfigurasi (sudah sesuai project kawulohalal-fcm) ───────────────────
    private const PROJECT_ID      = 'kawulohalal-fcm';
    private const SCOPE           = 'https://www.googleapis.com/auth/firebase.messaging';
    private const TOKEN_URI       = 'https://oauth2.googleapis.com/token';
    private const TOKEN_CACHE_KEY = 'fcm_v1_access_token';

    private static function fcmEndpoint(): string
    {
        return 'https://fcm.googleapis.com/v1/projects/' . self::PROJECT_ID . '/messages:send';
    }

    // ── Public: Kirim notifikasi ke satu device ───────────────────────────────
    /**
     * @param  string  $fcmToken  Token FCM device tujuan
     * @param  string  $title     Judul notifikasi
     * @param  string  $body      Isi / deskripsi notifikasi
     * @param  array   $data      Payload data tambahan (semua value harus string)
     */
    public static function send(
        string $fcmToken,
        string $title,
        string $body,
        array $data = []
    ): bool {
        try {
            $accessToken = self::getAccessToken();

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type'  => 'application/json',
            ])->post(self::fcmEndpoint(), [
                'message' => [
                    'token'        => $fcmToken,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    // ── Android config ──────────────────────────────────────
                    'android' => [
                        'priority'     => 'high',
                        'notification' => [
                            'channel_id'    => 'cashflow_channel', // sesuai channel Flutter
                            'sound'         => 'default',
                            'default_sound' => true,
                            'icon'          => 'ic_notification',  // drawable di res/drawable
                            'color'         => '#005FA3',          // warna brand biru
                        ],
                    ],
                    // ── iOS (APNs) config ────────────────────────────────────
                    'apns' => [
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                    'title' => $title,
                                    'body'  => $body,
                                ],
                                'sound' => 'default',
                                'badge' => 1,
                            ],
                        ],
                    ],
                    // ── Data payload (tersedia saat app foreground/background) ─
                    'data' => (object) array_map('strval', $data),
                    // FCM: semua value harus string
                ],
            ]);

            // ── Cek response ──────────────────────────────────────────────
            if ($response->failed()) {
                // Jika 401, access token mungkin expired — hapus cache
                if ($response->status() === 401) {
                    Cache::forget(self::TOKEN_CACHE_KEY);
                    Log::warning('[FCM] Access token expired, cache dihapus. Coba ulang.');
                }

                Log::error('[FCM] ❌ Gagal kirim notifikasi', [
                    'http_status' => $response->status(),
                    'response'    => $response->body(),
                    'token'       => substr($fcmToken, 0, 20) . '...',
                ]);
                return false;
            }

            Log::info('[FCM] ✅ Notifikasi berhasil terkirim', [
                'title'    => $title,
                'token'    => substr($fcmToken, 0, 20) . '...',
                'fcm_name' => $response->json('name'), // ID pesan dari FCM
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('[FCM] Exception saat kirim notifikasi', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    // ── Private: OAuth2 Access Token (cached 55 menit) ────────────────────────

    private static function getAccessToken(): string
    {
        // Cache selama 55 menit — FCM token expire setelah 60 menit
        return Cache::remember(self::TOKEN_CACHE_KEY, 55 * 60, function () {
            return self::fetchNewAccessToken();
        });
    }

    private static function fetchNewAccessToken(): string
    {
        $serviceAccount = self::loadServiceAccount();
        $jwt            = self::buildJwt($serviceAccount);

        $response = Http::asForm()->post(self::TOKEN_URI, [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if ($response->failed()) {
            throw new \RuntimeException(
                '[FCM] Gagal mendapatkan OAuth2 access token: ' . $response->body()
            );
        }

        $token = $response->json('access_token');

        if (empty($token)) {
            throw new \RuntimeException('[FCM] Access token kosong dari response OAuth2');
        }

        Log::info('[FCM] OAuth2 access token berhasil didapatkan');

        return $token;
    }

    // ── Private: Load Service Account JSON ───────────────────────────────────

    private static function loadServiceAccount(): array
    {
        $path = storage_path('app/firebase-service-account.json');

        if (!file_exists($path)) {
            throw new \RuntimeException(
                '[FCM] File service account tidak ditemukan di: ' . $path
            );
        }

        $content = file_get_contents($path);
        $data    = json_decode($content, true);

        if (
            !is_array($data)
            || empty($data['private_key'])
            || empty($data['client_email'])
        ) {
            throw new \RuntimeException(
                '[FCM] Format service account JSON tidak valid (private_key / client_email kosong)'
            );
        }

        return $data;
    }

    // ── Private: Build JWT RS256 ──────────────────────────────────────────────

    private static function buildJwt(array $serviceAccount): string
    {
        $now = time();

        $header  = self::base64url(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = self::base64url(json_encode([
            'iss'   => $serviceAccount['client_email'],
            'scope' => self::SCOPE,
            'aud'   => self::TOKEN_URI,
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $signingInput = "{$header}.{$payload}";

        $privateKey = openssl_pkey_get_private($serviceAccount['private_key']);

        if ($privateKey === false) {
            throw new \RuntimeException('[FCM] Gagal memuat private key dari service account');
        }

        openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return "{$signingInput}." . self::base64url($signature);
    }

    // ── Private: Base64 URL Encode ────────────────────────────────────────────

    private static function base64url(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
