<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserToken extends Model
{
    use HasFactory;

    protected $connection = 'owner';

    protected $fillable = [
        'user_id',
        'token',
        'expires_at',
    ];

    public function invalidToken() 
    {
        return $this->expires_at < now();
    }
}