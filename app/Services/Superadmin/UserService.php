<?php

namespace App\Services\Superadmin;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserService
{
    /**
     * Store a newly created User.
     *
     * Mirrors the original UserController@store logic exactly:
     * mass-assign the validated data as-is (password hashing is handled
     * transparently by the User model's `password` => 'hashed' cast).
     */
    public function store(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::create($data);
        });
    }

    /**
     * Update an existing User. Password is left unchanged when the field is
     * submitted empty, matching the edit form's "kosongkan jika tidak ingin
     * mengubah password" hint (password hashing is handled transparently by
     * the User model's `password` => 'hashed' cast).
     */
    public function update(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            if (empty($data['password'])) {
                unset($data['password']);
            }

            $user->update($data);

            return $user->fresh();
        });
    }

    /**
     * Delete a User.
     *
     * Mirrors the original UserController@destroy logic exactly.
     */
    public function delete(User $user): void
    {
        DB::transaction(function () use ($user) {
            $user->delete();
        });
    }
}
