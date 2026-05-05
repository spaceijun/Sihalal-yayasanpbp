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
    ];

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
     * Send a text message via WhatsApp.
     *
     * @param string      $number   Recipient number, e.g. 72888xxxx or 62888xxxx
     * @param string      $message  Message to be sent
     * @param string|null $footer   Optional footer under message
     * @param string      $method   'POST' (default) or 'GET'
     * @return array
     */
    public function sendText(
        string  $number,
        string  $message,
        ?string $footer = null,
        string  $method = 'POST'
    ): array {
        $params = [
            'api_key' => $this->apiKey,
            'sender'  => $this->sender,
            'number'  => $number,
            'message' => $message,
            'footer'  => $footer ?? '',
        ];

        Log::info('KawuloHalal Send Text Request', [
            'number' => $number,
            'footer' => $params['footer'],
        ]);

        return $this->makeRequest(self::ENDPOINTS['send_message'], $params, $method);
    }
}
