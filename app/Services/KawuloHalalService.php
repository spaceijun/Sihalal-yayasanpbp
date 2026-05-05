<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KawuloHalalService
{
    protected ?string $apiKey;
    protected ?string $sender;

    const ENDPOINTS = [
        'send_message' => 'https://gateway.kawulohalal.id/send-message',
        'send_media'   => 'https://gateway.kawulohalal.id/send-media',
    ];

    const ALLOWED_MEDIA_TYPES = ['image', 'video', 'audio', 'document'];

    public function __construct()
    {
        $this->apiKey = env('KAWULOHALAL_API_KEY');
        $this->sender = env('KAWULOHALAL_SENDER');
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
        ]);

        return $this->makeRequest(self::ENDPOINTS['send_media'], $params, $method);
    }

    /**
     * Shorthand: send a document.
     */
    public function sendDocument(string $number, string $url, ?string $caption = null, ?string $footer = null): array
    {
        return $this->sendMedia($number, 'document', $url, $caption, $footer);
    }
}
