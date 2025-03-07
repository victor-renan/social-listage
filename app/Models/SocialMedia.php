<?php

namespace App\Models;

use App\Enums\SocialMediaOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'token',
        'user_id',
    ];

    protected $hidden = [
        'token',
    ];

    protected function casts(): array
    {
        return [
            'name' => SocialMediaOptions::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
