<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait HasHashedId
{
    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }

    public static function findByHashedId($hashedId)
    {
        $decoded = Hashids::decode($hashedId);

        if (empty($decoded)) {
            return null;
        }

        return static::find($decoded[0]);
    }

    public static function findByHashedIdOrFail($hashedId)
    {
        $decoded = Hashids::decode($hashedId);

        if (empty($decoded)) {
            abort(404);
        }

        return static::findOrFail($decoded[0]);
    }
}
