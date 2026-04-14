<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataBank extends Model
{
    protected $table = 'data_banks';

    protected $fillable = [
        'name',
        'code',
    ];

    /**
     * Get the associated enumerators.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function enumerators()
    {
        return $this->hasMany(Enumerator::class, 'bank_id');
    }
}
