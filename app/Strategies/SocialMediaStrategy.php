<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;

interface SocialMediaStrategy
{
    public function posts(SocialMedia $instance): JsonResponse;

    public function validate(array $data): Validator;
}