<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Mendukung dua mode:
     *  - AJAX  (X-Requested-With: XMLHttpRequest)  → return JSON {redirect: '/url'}
     *  - Biasa (form submit standar)               → return RedirectResponse (fallback)
     */
    public function store(LoginRequest $request): JsonResponse|RedirectResponse
    {
        // Autentikasi — melempar ValidationException jika gagal (422 otomatis untuk AJAX)
        $request->authenticate();
        // Regenerasi session untuk mencegah session fixation
        Session::regenerate();

        // Tentukan URL tujuan berdasarkan role
        $url = $this->resolveRedirectUrl(Auth::user()->role);

        // Jika request dari AJAX (fetch di blade), kembalikan JSON
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $url,
                'role' => Auth::user()->role,
            ]);
        }

        // Fallback: redirect biasa (jika JS dinonaktifkan)
        return redirect()->intended($url);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Resolve redirect URL berdasarkan role user.
     */
    private function resolveRedirectUrl(string $role): string
    {
        return match ($role) {
            'superadmin' => '/superadmin',
            'koordinator' => '/koordinator',
            'data_entry' => '/data-entry',
            'enumerator' => '/enumerator',
            'admin_umum' => '/admin-umum',
            default => '/dashboard',
        };
    }
}
