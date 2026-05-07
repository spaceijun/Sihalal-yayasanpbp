<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KawuloHalalService
{
    protected ?string $apiKey;
    protected ?string $sender;

    const ENDPOINTS = [
        'send_message' => 'https://kawalakugateway.cloud/send-message',
        'send_media'   => 'https://kawalakugateway.cloud/send-media',
    ];

    const ALLOWED_MEDIA_TYPES = ['image', 'video', 'audio', 'document'];

    public function __construct()
    {
        $this->apiKey = config('services.kawulohalal.api_key');
        $this->sender = config('services.kawulohalal.sender');
    }

    /**
     * Make a POST or GET request to the given endpoint.
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
     * Send a plain text message via WhatsApp.
     *
     * @param string $number   Recipient number, e.g. 72888xxxx or 62888xxxx
     * @param string $message  Message to be sent
     * @param string $method   'POST' (default) or 'GET'
     * @return array
     */
    public function sendText(
        string $number,
        string $message,
        string $method = 'POST'
    ): array {
        $params = [
            'api_key' => $this->apiKey,
            'sender'  => $this->sender,
            'number'  => $number,
            'message' => $message,
        ];

        Log::info('KawuloHalal Send Text Request', [
            'number'          => $number,
            'message_length'  => strlen($message),
            'message_preview' => substr($message, 0, 100),
            'has_api_key'     => !empty($this->apiKey),
            'has_sender'      => !empty($this->sender),
        ]);

        return $this->makeRequest(self::ENDPOINTS['send_message'], $params, $method);
    }

    /**
     * Send media (image, video, audio, document) via WhatsApp.
     *
     * @param string      $number     Recipient number, e.g. 72888xxxx or 62888xxxx
     * @param string      $mediaType  One of: image, video, audio, document
     * @param string      $url        Direct public URL to the media file
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

        if (!is_null($footer)) {
            $params['footer'] = $footer;
        }

        Log::info('KawuloHalal Send Media Request', [
            'number'         => $number,
            'media_type'     => $mediaType,
            'url'            => $url,
            'caption_length' => strlen($caption ?? ''),
            'has_api_key'    => !empty($this->apiKey),
            'has_sender'     => !empty($this->sender),
        ]);

        return $this->makeRequest(self::ENDPOINTS['send_media'], $params, $method);
    }

    /**
     * Send media with retry logic on failure.
     *
     * @param string      $number       Recipient number
     * @param string      $mediaType    One of: image, video, audio, document
     * @param string      $url          Direct public URL to the media file
     * @param string|null $caption      Optional caption/message
     * @param string|null $footer       Optional footer text
     * @param int         $maxRetry     Maximum number of attempts (default: 3)
     * @param int         $delaySeconds Delay in seconds between retries (default: 2)
     * @return array
     */
    public function sendMediaWithRetry(
        string  $number,
        string  $mediaType,
        string  $url,
        ?string $caption      = null,
        ?string $footer       = null,
        int     $maxRetry     = 3,
        int     $delaySeconds = 2
    ): array {
        $attempt = 0;
        $result  = ['status' => false, 'error' => 'No attempt made'];

        while ($attempt < $maxRetry) {
            $attempt++;
            $result = $this->sendMedia($number, $mediaType, $url, $caption, $footer);

            if ($result['status'] === true) {
                return $result;
            }

            Log::warning('KawuloHalal: retry attempt', [
                'attempt'    => $attempt,
                'max'        => $maxRetry,
                'number'     => $number,
                'media_type' => $mediaType,
                'url'        => $url,
                'error'      => $result['error'] ?? '-',
            ]);

            if ($attempt < $maxRetry) {
                sleep($delaySeconds);
            }
        }

        Log::error('KawuloHalal: semua retry gagal', [
            'number'     => $number,
            'media_type' => $mediaType,
            'url'        => $url,
            'max_retry'  => $maxRetry,
            'error'      => $result['error'] ?? '-',
        ]);

        return $result;
    }

    /**
     * Shorthand: send a document with retry.
     */
    public function sendDocument(string $number, string $url, ?string $caption = null, ?string $footer = null): array
    {
        return $this->sendMediaWithRetry($number, 'document', $url, $caption, $footer);
    }
}
