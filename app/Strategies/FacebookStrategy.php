<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;

class FacebookStrategy implements SocialMediaStrategy
{
    protected string $apiUrl = 'https://graph.facebook.com/v22.0/me/posts';

    public function posts(SocialMedia $instance): JsonResponse
    {
        $params = [
            'access_token' => $instance->token,
            'fields' => 'actions,attachments{description,media,media_type,title,subattachments,description_tags,target,type,unshimmed_url,url},caption,is_hidden,is_expired,message,from,place,status_type,reactions,created_time,link',
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