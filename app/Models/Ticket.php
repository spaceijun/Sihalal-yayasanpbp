<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Ticket
 *
 * @property $id
 * @property $user_id
 * @property $no_ticket
 * @property $subject
 * @property $description
 * @property $file
 * @property $status
 * @property $created_at
 * @property $updated_at
 *
 * @property User $user
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Ticket extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['user_id', 'no_ticket', 'subject', 'description', 'file', 'status'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
