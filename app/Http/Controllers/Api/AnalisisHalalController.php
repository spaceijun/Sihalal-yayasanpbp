<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalisisHalalController extends Controller
{
    /**
     * Proxy request ke Anthropic Claude API.
     * API key disimpan aman di server, tidak pernah terekspos ke frontend.
     *
     * POST /api/analisis-halal
     * Body: { base64?: string, mimeType?: string, prompt: string, textOnly?: bool }
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'base64'    => 'nullable|string',
            'mimeType'  => 'nullable|string|in:image/jpeg,image/jpg,image/png,image/webp,image/gif',
            'prompt'    => 'required|string|max:5000',
            'textOnly'  => 'nullable|boolean',
        ]);

        $apiKey = config('services.anthropic.api_key');
        if (empty($apiKey)) {
            return response()->json(
                ['message' => 'Konfigurasi API key tidak ditemukan.'],
                500
            );
        }

        // Tentukan content berdasarkan mode teks saja atau dengan gambar
        if ($request->textOnly || empty($request->base64)) {
            $content = [['type' => 'text', 'text' => $request->prompt]];
        } else {
            $content = [
                ['type' => 'image', 'source' => ['type' => 'base64', 'media_type' => $request->mimeType, 'data' => $request->base64]],
                ['type' => 'text', 'text' => $request->prompt],
            ];
        }

        $response = Http::withHeaders([
            'x-api-key'         => $apiKey,
            'anthropic-version' => '2023-06-01',
            'content-type'      => 'application/json',
        ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
            'model'      => 'claude-haiku-4-5-20251001',
            'max_tokens' => 4096,
            'messages'   => [
                [
                    'role'    => 'user',
                    'content' => $content,
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
