<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

/**
 * Trait untuk standardized API responses dengan error handling yang aman.
 * Mencegah leakage informasi sensitif melalui error messages.
 */
trait ApiResponses
{
    /**
     * Return success response
     */
    protected function successResponse(array $data = [], string $message = null, int $statusCode = 200): \Illuminate\Http\JsonResponse
    {
        $response = ['success' => true];

        if ($message) {
            $response['message'] = $message;
        }

        return response()->json(array_merge($response, $data), $statusCode);
    }

    /**
     * Return error response - TIDAK mengembalikan detail error ke client
     */
    protected function errorResponse(string $message, int $statusCode = 400): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $statusCode);
    }

    /**
     * Log error dan return generic error response ke client
     */
    protected function safeErrorResponse(\Exception $e, string $userMessage = 'Terjadi kesalahan sistem', int $statusCode = 500): \Illuminate\Http\JsonResponse
    {
        // Log full error untuk debugging (hanya di server, tidak ke client)
        Log::error('API Error: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ]);

        // Return generic message ke client
        return $this->errorResponse($userMessage, $statusCode);
    }

    /**
     * Validation error response
     */
    protected function validationErrorResponse($validator): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $validator->errors(),
        ], 422);
    }
}
