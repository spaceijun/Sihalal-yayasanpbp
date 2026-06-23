<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Superadmin\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(protected ProfileService $profileService) {}

    /**
     * Tampilkan halaman ubah profil.
     */
    public function edit(Request $request): View
    {
        return view('superadmin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Perbarui informasi profil (nama & email).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->profileService->updateInfo(
            $request->user(),
            $request->validated()
        );

        return Redirect::route('superadmin.profile.edit')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Perbarui password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $this->profileService->updatePassword($request->user(), $request->input('password'));

        return Redirect::route('superadmin.profile.edit')
            ->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Hapus akun pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
