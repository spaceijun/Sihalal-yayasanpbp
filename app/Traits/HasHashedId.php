<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashedId
{
    public function getHashedIdAttribute()
    {
        $hash = Hashids::encode($this->id);

        // tambahkan random jika kurang dari 50 karakter
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        while (strlen($hash) < 50) {
            $hash .= $characters[rand(0, strlen($characters) - 1)];
        }

        // 50 karakter
        $hash = substr($hash, 0, 50);

        // format  XXXXX-XXXXX-XXXXX
        return implode('-', str_split($hash, 10));
    }

    public static function findByHashedId($hashedId)
    {
        // hapus strip dulu
        $cleanHash = str_replace('-', '', $hashedId);

        $decoded = Hashids::decode($cleanHash);

        if (empty($decoded)) {
            return null;
        }

        return static::find($decoded[0]);
    }

    public static function findByHashedIdOrFail($hashedId)
    {
        $cleanHash = str_replace('-', '', $hashedId);

        $decoded = Hashids::decode($cleanHash);

        if (empty($decoded)) {
            abort(404);
        }

        return static::findOrFail($decoded[0]);
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return static::findByHashedIdOrFail($value);
    }
}
