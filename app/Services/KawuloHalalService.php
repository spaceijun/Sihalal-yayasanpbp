<?php

namespace App\Services;

use App\Models\Superadmin\WaGatewayConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * KawuloHalalService
 *
 * Mengirim pesan WhatsApp melalui Kawalaku Gateway API.
 * Endpoint pesan: POST /api/v1/messages/send/text | /api/v1/messages/send/media
 * Endpoint device: POST /api/device/* | GET /api/device/* | PATCH /api/device/:id/features
 * Auth     : Bearer token (API key dari dashboard kawalakugateway)
 *
 * Pattern: Service Layer dengan Dependency Injection
 * Base URL: Konfigurasi dari database (wa_gateway_configs table)
 *
 * Catatan: Error 500 sebelumnya disebabkan Node.js Baileys service di sisi
 * Kawalaku Gateway belum berjalan, bukan karena endpoint salah. Sekarang
 * sudah diperbaiki oleh developer.
 */
class KawuloHalalService
{
    /**
     * Allowed media types
     */
    const ALLOWED_MEDIA_TYPES = ['image', 'video', 'audio', 'document'];

    protected ?string $apiKey;

    protected string $baseUrl;

    protected string $nodeJsUrl;

    protected bool $enabled;

    protected bool $bypassSsl;

    public function __construct()
    {
        // Baca konfigurasi dari database
        $configs = WaGatewayConfig::getAll();

        // Base URL untuk Kawalaku Gateway Laravel API
        $this->baseUrl = rtrim($configs['base_url'] ?? config('services.kawulohalal.base_url', 'http://kawalakugateway.test'), '/');

        // Base URL untuk Node.js Baileys service (WhatsApp service)
        $this->nodeJsUrl = rtrim($configs['wa_gateway_url'] ?? config('services.wa_gateway.url', 'http://localhost:3000'), '/');

        // API Key
        $this->apiKey = $configs['api_key'] ?? config('services.kawulohalal.api_key');

        // Enabled status
        $this->enabled = (bool) ($configs['enabled'] ?? true);

        // Bypass SSL status
        $this->bypassSsl = (bool) ($configs['bypass_ssl'] ?? false);
    }

    /**
     * Bangun URL endpoint dari path relatif (Laravel API) — dengan prefix /api/v1/.
     */
    protected function apiEndpoint(string $path): string
    {
        return $this->baseUrl.'/api/v1/'.ltrim($path, '/');
    }

    /**
     * Bangun URL endpoint di root Laravel API — tanpa prefix /api/v1/.
     * Digunakan untuk endpoint baru: /send-text, /send-media.
     */
    protected function rootEndpoint(string $path): string
    {
        return $this->baseUrl.'/'.ltrim($path, '/');
    }

    /**
     * Bangun URL endpoint untuk Node.js Baileys service.
     */
    protected function baileysEndpoint(string $path): string
    {
        return $this->nodeJsUrl.'/'.ltrim($path, '/');
    }

    /**
     * Make a request to Kawalaku Gateway API (Bearer auth).
     */
    protected function makeApiRequest(string $endpoint, array $params = [], string $method = 'POST'): array
    {
        if (! $this->apiKey) {
            return ['status' => false, 'error' => 'API key is required.'];
        }

        try {
            $http = Http::withToken($this->apiKey)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->timeout(15)
                ->connectTimeout(5);

            if ($this->bypassSsl) {
                $http = $http->withoutVerifying();
            }

            $response = match (strtoupper($method)) {
                'GET' => $http->get($endpoint, $params),
                'POST' => $http->post($endpoint, $params),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            $responseJson = $response->json();
            $logContext = [
                'endpoint' => $endpoint,
                'method' => $method,
                'http_status' => $response->status(),
                'response' => $responseJson,
            ];

            if ($response->failed()) {
                Log::warning('KawuloHalal API: gateway menolak request', $logContext);

                return [
                    'status' => false,
                    'error' => $responseJson['message'] ?? 'Unknown error occurred',
                ];
            }

            // Kawalaku Gateway returns { "success": true/false, "message": "...", "data": {...} }
            if (isset($responseJson['success']) && $responseJson['success'] === false) {
                Log::warning('KawuloHalal API: gateway menolak request', $logContext);

                return [
                    'status' => false,
                    'error' => $responseJson['message'] ?? 'Gateway menolak pesan',
                ];
            }

            Log::info('KawuloHalal API Response', $logContext);

            return [
                'status' => true,
                'data' => $responseJson['data'] ?? $responseJson,
            ];
        } catch (\Exception $e) {
            Log::error('KawuloHalal API Exception', ['message' => $e->getMessage()]);

            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Make a request to Node.js Baileys service (direct WhatsApp service).
     */
    protected function makeBaileysRequest(string $endpoint, array $params = [], string $method = 'POST'): array
    {
        try {
            $http = Http::timeout(30)
                ->connectTimeout(10);

            if ($this->bypassSsl) {
                $http = $http->withoutVerifying();
            }

            $response = match (strtoupper($method)) {
                'GET' => $http->get($endpoint, $params),
                'POST' => $http->post($endpoint, $params),
                'PATCH' => $http->patch($endpoint, $params),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            $responseJson = $response->json();

            if ($response->failed()) {
                Log::warning('KawuloHalal Baileys: request gagal', [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'error' => $responseJson['error'] ?? $responseJson['message'] ?? $response->body(),
                ]);

                return [
                    'status' => false,
                    'error' => $responseJson['error'] ?? $responseJson['message'] ?? 'Request failed',
                ];
            }

            // Check jika Baileys service mengembalikan success: false
            // Ini bisa terjadi meskipun HTTP status 200 (misal: "Device not connected")
            if (isset($responseJson['success']) && $responseJson['success'] === false) {
                $errorMessage = $responseJson['message'] ?? 'Device not connected';
                Log::warning('KawuloHalal Baileys: request gagal', [
                    'endpoint' => $endpoint,
                    'method' => $method,
                    'error' => $errorMessage,
                ]);

                return [
                    'status' => false,
                    'error' => $errorMessage,
                ];
            }

            return [
                'status' => true,
                'data' => $responseJson,
            ];
        } catch (\Exception $e) {
            Log::error('KawuloHalal Baileys Exception', ['message' => $e->getMessage()]);

            return [
                'status' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public API - Send Message Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Send a plain text message via WhatsApp.
     *
     * @param  string  $number  Recipient number, e.g. 628xxxx
     * @param  string  $message  Message to be sent
     * @param  string  $deviceId  Device ID (hashed) to send from
     */
    public function sendText(
        string $number,
        string $message,
        ?string $deviceId = null
    ): array {
        if (! $this->enabled) {
            return ['status' => false, 'error' => 'WhatsApp Gateway is disabled'];
        }

        // Gunakan Kawalaku Gateway API — route: POST /api/v1/messages/send/text
        // Error 500 sebelumnya karena Node.js Baileys di sisi mereka belum jalan,
        // bukan karena route salah. Sekarang sudah diperbaiki oleh developer.

        // Jika tidak ada device_id, cari device pertama yang terhubung (DB → fallback API)
        if (! $deviceId) {
            $deviceId = $this->resolveConnectedDeviceId();
        }

        Log::info('KawuloHalal Send Text via API', [
            'device_id' => $deviceId,
            'number' => $number,
            'message_length' => strlen($message),
            'has_api_key' => ! empty($this->apiKey),
            'base_url' => $this->baseUrl,
        ]);

        $params = [
            'device_hashed_id' => $deviceId ?? '',
            'receiver_number' => $this->normalizeNumber($number),
            'message_template' => $message,
        ];

        return $this->makeApiRequest($this->apiEndpoint('messages/send/text'), $params, 'POST');
    }

    /**
     * Send media (image, video, audio, document) via WhatsApp.
     *
     * @param  string  $number  Recipient number, e.g. 628xxxx
     * @param  string  $mediaType  One of: image, video, audio, document
     * @param  string  $url  Direct public URL to the media file
     * @param  string|null  $caption  Optional caption/message
     * @param  string|null  $footer  Optional footer text (appended to caption)
     * @param  string|null  $deviceId  Device ID (hashed) to send from
     */
    public function sendMedia(
        string $number,
        string $mediaType,
        string $url,
        ?string $caption = null,
        ?string $footer = null,
        ?string $deviceId = null
    ): array {
        if (! $this->enabled) {
            return ['status' => false, 'error' => 'WhatsApp Gateway is disabled'];
        }

        if (! in_array($mediaType, self::ALLOWED_MEDIA_TYPES)) {
            return [
                'status' => false,
                'error' => "Invalid media_type '{$mediaType}'. Allowed: ".implode(', ', self::ALLOWED_MEDIA_TYPES),
            ];
        }

        // Gabungkan caption & footer
        $fullCaption = $caption ?? '';
        if (! is_null($footer) && $footer !== '') {
            $fullCaption .= ($fullCaption ? "\n\n" : '').$footer;
        }

        // Jika tidak ada device_id, cari device pertama yang terhubung (DB → fallback API)
        if (! $deviceId) {
            $deviceId = $this->resolveConnectedDeviceId();
        }

        Log::info('KawuloHalal Send Media Request', [
            'number' => $number,
            'media_type' => $mediaType,
            'url' => $url,
            'caption_length' => strlen($fullCaption),
            'has_api_key' => ! empty($this->apiKey),
        ]);

        $params = [
            'device_hashed_id' => $deviceId ?? '',
            'receiver_number' => $this->normalizeNumber($number),
            'media_type' => $mediaType,
            'media_url' => $url,
            'message_template' => $fullCaption ?: '',
        ];

        return $this->makeApiRequest($this->apiEndpoint('messages/send/media'), $params, 'POST');
    }

    /**
     * Send media with retry logic on failure.
     */
    public function sendMediaWithRetry(
        string $number,
        string $mediaType,
        string $url,
        ?string $caption = null,
        ?string $footer = null,
        ?string $deviceId = null,
        int $maxRetry = 3,
        int $delaySeconds = 2
    ): array {
        $attempt = 0;
        $result = ['status' => false, 'error' => 'No attempt made'];

        while ($attempt < $maxRetry) {
            $attempt++;
            $result = $this->sendMedia($number, $mediaType, $url, $caption, $footer, $deviceId);

            if ($result['status'] === true) {
                return $result;
            }

            Log::warning('KawuloHalal: retry attempt', [
                'attempt' => $attempt,
                'max' => $maxRetry,
                'number' => $number,
                'media_type' => $mediaType,
                'url' => $url,
                'error' => $result['error'] ?? '-',
            ]);

            if ($attempt < $maxRetry) {
                sleep($delaySeconds);
            }
        }

        Log::error('KawuloHalal: semua retry gagal', [
            'number' => $number,
            'media_type' => $mediaType,
            'url' => $url,
            'max_retry' => $maxRetry,
            'error' => $result['error'] ?? '-',
        ]);

        return $result;
    }

    /**
     * Shorthand: send a document with retry.
     */
    public function sendDocument(
        string $number,
        string $url,
        ?string $caption = null,
        ?string $footer = null,
        ?string $deviceId = null
    ): array {
        return $this->sendMediaWithRetry($number, 'document', $url, $caption, $footer, $deviceId);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Device Management Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Connect a device to WhatsApp (generate QR code).
     */
    public function connectDevice(string $deviceId): array
    {
        return $this->makeBaileysRequest($this->baileysEndpoint('api/device/connect'), [
            'device_id' => $deviceId,
        ]);
    }

    /**
     * Disconnect a device from WhatsApp.
     */
    public function disconnectDevice(string $deviceId): array
    {
        return $this->makeBaileysRequest($this->baileysEndpoint('api/device/disconnect'), [
            'device_id' => $deviceId,
        ]);
    }

    /**
     * Get device connection status.
     */
    public function getDeviceStatus(string $deviceId): array
    {
        return $this->makeBaileysRequest($this->baileysEndpoint('api/device/status'), [
            'device_id' => $deviceId,
        ], 'GET');
    }

    /**
     * Get QR code status for a device.
     */
    public function getQrStatus(string $deviceId): array
    {
        return $this->makeBaileysRequest($this->baileysEndpoint('api/device/qr'), [
            'device_id' => $deviceId,
        ], 'GET');
    }

    /**
     * Force reconnect (delete session and reconnect).
     */
    public function forceReconnect(string $deviceId): array
    {
        return $this->makeBaileysRequest($this->baileysEndpoint('api/device/force-reconnect'), [
            'device_id' => $deviceId,
        ]);
    }

    /**
     * Update device features (reject_call, available, typing).
     */
    public function updateDeviceFeatures(string $deviceId, array $features): array
    {
        return $this->makeBaileysRequest(
            $this->baileysEndpoint("api/device/{$deviceId}/features"),
            ['features' => $features],
            'PATCH'
        );
    }

    /**
     * Get device features.
     */
    public function getDeviceFeatures(string $deviceId): array
    {
        return $this->makeBaileysRequest(
            $this->baileysEndpoint("api/device/{$deviceId}/features"),
            [],
            'GET'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────
    // API Key Based Methods (for Laravel API)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Get list of connected devices via API.
     */
    public function getDevices(): array
    {
        return $this->makeApiRequest($this->apiEndpoint('devices'), [], 'GET');
    }

    /**
     * Get message status via API.
     */
    public function getMessageStatus(string $messageId): array
    {
        return $this->makeApiRequest($this->apiEndpoint("messages/status/{$messageId}"), [], 'GET');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helper Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Resolve device ID yang sedang connected.
     * Urutan: DB lokal → fallback remote API → null.
     * Jika device ditemukan dari API tapi tidak ada di DB, update DB agar sync.
     */
    private function resolveConnectedDeviceId(): ?string
    {
        // 1. Cari di DB lokal dulu
        $device = \App\Models\Superadmin\WaDevice::where('status', 'connected')->first();
        if ($device) {
            Log::info('KawuloHalal: Auto-selected device from DB', ['device_id' => $device->hashed_id]);

            return $device->hashed_id;
        }

        // 2. Fallback: tanya langsung ke remote API
        try {
            $response = $this->makeApiRequest($this->apiEndpoint('devices'), [], 'GET');
            $devices = $response['data']['devices'] ?? [];

            foreach ($devices as $remoteDevice) {
                if (($remoteDevice['status'] ?? '') === 'connected') {
                    $remoteId = $remoteDevice['id'];

                    // Sync ke DB: update device yang hashed_id-nya cocok (jika ada)
                    \App\Models\Superadmin\WaDevice::where('hashed_id', $remoteId)
                        ->update(['status' => 'connected']);

                    Log::info('KawuloHalal: Auto-selected device from remote API (DB was stale)', [
                        'device_id' => $remoteId,
                    ]);

                    return $remoteId;
                }
            }
        } catch (\Exception $e) {
            Log::warning('KawuloHalal: gagal resolve device dari API', ['error' => $e->getMessage()]);
        }

        Log::warning('KawuloHalal: tidak ada device connected (DB maupun API)');

        return null;
    }

    /**
     * Normalize phone number to 62xxx format.
     * Accepts: 08xxx, 628xxx, +628xxx, 8xxx
     */
    public function normalizeNumber(string $number): string
    {
        // Hapus karakter non-digit (kecuali leading +)
        $number = preg_replace('/[^\d]/', '', $number);

        if (str_starts_with($number, '0')) {
            return '62'.substr($number, 1);
        }

        if (! str_starts_with($number, '62')) {
            return '62'.$number;
        }

        return $number;
    }

    /**
     * Check if the service is available.
     */
    public function healthCheck(): bool
    {
        if (! $this->enabled) {
            return false;
        }

        try {
            $http = Http::timeout(5);
            if ($this->bypassSsl) {
                $http = $http->withoutVerifying();
            }
            $response = $http->get($this->nodeJsUrl);

            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if WhatsApp Gateway is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Get default media URL for notifications.
     */
    public function getDefaultMediaUrl(): string
    {
        $configs = WaGatewayConfig::getAll();

        return $configs['default_media_url'] ?? 'https://kawulohalal.id/assets/logo.png';
    }
}
