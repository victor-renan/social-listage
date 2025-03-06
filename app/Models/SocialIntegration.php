<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialIntegration extends Model
{
    protected $fillable = [
        'name',
        'token',
        'user_id',
    ];

    protected $hidden = [
        'token',
    ];
}
