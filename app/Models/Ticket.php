<?php

namespace App\Models;

use App\Traits\HasHashedId;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasHashedId;

    protected $perPage = 20;

    protected $fillable = ['user_id', 'no_ticket', 'subject', 'description', 'file', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
