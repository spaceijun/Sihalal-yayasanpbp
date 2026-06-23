<?php

namespace App\Services\Superadmin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    /**
     * Perbarui informasi profil pengguna (nama & email).
     */
    public function updateInfo(User $user, array $data): User
    {
        $user->name = $data['name'];
        $user->email = $data['email'];

        // Reset verifikasi email jika email berubah
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return $user;
    }

    /**
     * Perbarui password pengguna.
     */
    public function updatePassword(User $user, string $newPassword): void
    {
        $user->password = Hash::make($newPassword);
        $user->save();
    }
}
