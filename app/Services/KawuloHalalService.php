<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KawuloHalalService
{
    protected ?string $apiKey;
    protected ?string $sender;

    const ENDPOINTS = [
        'send_media' => 'https://gateway.kawulohalal.id/send-media',
    ];

    const ALLOWED_MEDIA_TYPES = ['image', 'video', 'audio', 'document'];

    public function __construct()
    {
        $this->apiKey = env('KAWULOHALAL_API_KEY');
        $this->sender = env('KAWULOHALAL_SENDER');
    }

    /**
     * Make a POST or GET request to the given endpoint.
     *
     * @param string $endpoint
     * @param array  $params
     * @param string $method  'POST' | 'GET'
     * @return array
     */
    protected function makeRequest(string $endpoint, array $params = [], string $method = 'POST'): array
    {
        if (!$this->apiKey) {
            return ['status' => false, 'error' => 'API key is required.'];
        }

        try {
            $http = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ])->timeout(10)->connectTimeout(5);

            $response = match (strtoupper($method)) {
                'GET'   => $http->get($endpoint, $params),
                'POST'  => $http->post($endpoint, $params),
                default => throw new \InvalidArgumentException("Unsupported HTTP method: {$method}"),
            };

            $responseJson = $response->json();
            $logContext   = [
                'endpoint'    => $endpoint,
                'method'      => $method,
                'http_status' => $response->status(),
                'response'    => $responseJson,
            ];

            if (isset($responseJson['status']) && $responseJson['status'] === false) {
                Log::warning('KawuloHalal API: gateway menolak request', $logContext);
            } else {
                Log::info('KawuloHalal API Response', $logContext);
            }

            if ($response->failed()) {
                return [
                    'status' => false,
                    'error'  => $responseJson['msg'] ?? $responseJson['message'] ?? 'Unknown error occurred',
                ];
            }

            if (isset($responseJson['status']) && $responseJson['status'] === false) {
                return [
                    'status' => false,
                    'error'  => $responseJson['msg'] ?? $responseJson['message'] ?? 'Gateway menolak pesan',
                ];
            }

            return [
                'status' => true,
                'data'   => $responseJson,
            ];
        } catch (\Exception $e) {
            Log::error('KawuloHalal API Exception', ['message' => $e->getMessage()]);

            return [
                'status' => false,
                'error'  => $e->getMessage(),
            ];
        }
    }

    /**
     * Send media (image, video, audio, document) via WhatsApp.
     *
     * @param string      $number     Recipient number, e.g. 72888xxxx or 62888xxxx
     * @param string      $mediaType  One of: image, video, audio, document
     * @param string      $url        Direct URL to the media file
     * @param string|null $caption    Optional caption/message
     * @param string|null $footer     Optional footer text
     * @param string      $method     'POST' (default) or 'GET'
     * @return array
     */
    public function sendMedia(
        string  $number,
        string  $mediaType,
        string  $url,
        ?string $caption = null,
        ?string $footer  = null,
        string  $method  = 'POST'
    ): array {
        if (!in_array($mediaType, self::ALLOWED_MEDIA_TYPES)) {
            return [
                'status' => false,
                'error'  => "Invalid media_type '{$mediaType}'. Allowed: " . implode(', ', self::ALLOWED_MEDIA_TYPES),
            ];
        }

        $params = [
            'api_key'    => $this->apiKey,
            'sender'     => $this->sender,
            'number'     => $number,
            'media_type' => $mediaType,
            'url'        => $url,
        ];

        if (!is_null($caption)) {
            $params['caption'] = $caption;
        }

        // Footer sengaja tidak dikirim — gateway memiliki bug saat memproses property footer
        // if (!is_null($footer)) { $params['footer'] = $footer; }

        // Pastikan URL bisa diakses publik sebelum dikirim ke gateway
        $urlCheck = $this->assertPublicUrl($url);
        if ($urlCheck !== null) {
            return $urlCheck;
        }

        Log::info('KawuloHalal Send Media Request', ['params' => $params]);

        return $this->makeRequest(self::ENDPOINTS['send_media'], $params, $method);
    }

    /**
     * Shorthand: send an image.
     */
    public function sendImage(string $number, string $url, ?string $caption = null, ?string $footer = null): array
    {
        return $this->sendMedia($number, 'image', $url, $caption, $footer);
    }

    /**
     * Shorthand: send a video.
     */
    public function sendVideo(string $number, string $url, ?string $caption = null, ?string $footer = null): array
    {
        return $this->sendMedia($number, 'video', $url, $caption, $footer);
    }

    /**
     * Shorthand: send an audio file.
     */
    public function sendAudio(string $number, string $url, ?string $caption = null): array
    {
        return $this->sendMedia($number, 'audio', $url, $caption);
    }

    /**
     * Shorthand: send a document.
     */
    public function sendDocument(string $number, string $url, ?string $caption = null, ?string $footer = null): array
    {
        return $this->sendMedia($number, 'document', $url, $caption, $footer);
    }

    /**
     * Validasi bahwa URL bersifat publik (bukan localhost / .test / .local).
     * Kembalikan array error jika lokal, null jika aman.
     */
    protected function assertPublicUrl(string $url): ?array
    {
        $host = parse_url($url, PHP_URL_HOST) ?? '';

        $isLocal = str_ends_with($host, '.test')
            || str_ends_with($host, '.local')
            || str_ends_with($host, '.localhost')
            || in_array($host, ['localhost', '127.0.0.1', '::1']);

        if ($isLocal) {
            $message = "URL media bersifat lokal ({$host}) dan tidak dapat diakses oleh gateway eksternal. "
                . "Gunakan URL publik atau expose via ngrok/tunnel.";

            Log::error('KawuloHalal URL lokal terdeteksi', ['url' => $url, 'host' => $host]);

            return ['status' => false, 'error' => $message];
        }

        return null;
    }
}
