<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnalisisHalalController extends Controller
{
    /**
     * Proxy request ke Anthropic Claude API.
     * API key disimpan aman di server, tidak pernah terekspos ke frontend.
     *
     * POST /api/analisis-halal
     * Body: { base64: string, mimeType: string, prompt: string }
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'base64'   => 'required|string',
            'mimeType' => 'required|string|in:image/jpeg,image/jpg,image/png,image/webp,image/gif',
            'prompt'   => 'required|string|max:5000',
        ]);

        $apiKey = config('services.anthropic.api_key');

        if (empty($apiKey)) {
            return response()->json(
                ['message' => 'Konfigurasi API key tidak ditemukan.'],
                500
            );
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-sonnet-4-20250514',
            'max_tokens' => 1024,
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => [
                        [
                            'type'   => 'image',
                            'source' => [
                                'type'       => 'base64',
                                'media_type' => $request->mimeType,
                                'data'       => $request->base64,
                            ],
                        ],
                        [
                            'type' => 'text',
                            'text' => $request->prompt,
                        ],
                    ],
                ],
            ],
        ]);

        if ($response->failed()) {
            $errorBody = $response->json();
            $message   = $errorBody['error']['message'] ?? 'Gagal menghubungi Claude API.';
            return response()->json(['message' => $message], $response->status());
        }

        // Teruskan respons Claude langsung ke frontend
        return response()->json($response->json());
    }
}
