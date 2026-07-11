<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle an incoming authentication request (API Login).
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Revoke old tokens (optional: single session)
        $user->tokens()->delete();

        // Token enumerator berlaku 7 hari; role lain ikut expiration global Sanctum
        $expiresAt = $user->role === 'enumerator' ? now()->addDays(7) : null;
        $token     = $user->createToken('auth_token', ['*'], $expiresAt)->plainTextToken;

        return response()->json([
            'status'  => true,
            'message' => 'Login berhasil',
            'data'    => [
                'user'         => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'role'  => $user->role,
                ],
                'token'        => $token,
                'token_type'   => 'Bearer',
                'expires_at'   => $expiresAt?->toDateTimeString(),   // null = ikut global Sanctum
                'redirect_url' => $this->getRedirectUrl($user->role),
            ],
        ], 200);
    }

    /**
     * Get redirect URL based on role.
     */
    private function getRedirectUrl(string $role): string
    {
        return match ($role) {
            'superadmin'  => 'superadmin',
            'koordinator' => 'koordinator',
            'data_entry'  => 'data-entry',
            'enumerator'  => 'enumerator',
            default       => 'dashboard',
        };
    }
}
