<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;

class InstagramStrategy implements SocialMediaStrategy
{
    protected string $apiUrl = 'https://graph.instagram.com/v22.0/me/media';

    public function posts(SocialMedia $instance): JsonResponse
    {
        $params = [
            'access_token' => $instance->token,
            'fields' => 'permalink,id,thumbnail_url,media_type,media_url,username,owner{name,username},timestamp,like_count,comments_count,caption',
        ];

        $response = Http::timeout(5)
            ->withQueryParameters($params)
            ->get($this->apiUrl);

        return response()->json([
            'type' => $instance->type,
            'social' => $response->json()
        ], $response->status());
    }

    public function validate(array $data): Validator
    {
        $common = SocialMedia::validation($data);

        return \Validator::make(
            $data,
            $common->rules,
            $common->messages
        );
    }
}