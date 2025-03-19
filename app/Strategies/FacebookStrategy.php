<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Http;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;

class FacebookStrategy implements SocialMediaStrategy
{
    protected string $apiUrl = 'https://graph.facebook.com/v22.0/me/posts';
    protected string $reactionsUrl = 'https://graph.facebook.com/v22.0/';

    protected function postsAdapter(array $json, string $token): array
    {
        return array_map(function (array $item) use ($token) {
            $likes = Http::timeout(5)
                ->withQueryParameters([
                    'type' => 'LIKE',
                    'summary' => 'total_counts',
                    'access_token' => $token,
                ])->get($this->reactionsUrl . $item['id']);
            return [
                'url' => $item['link'],
                'image_url' => $item['full_picture'],
                'likes' => $likes->ok() ? $likes->json()['summary']['total_counts'] : 0
            ];
        }, $json['data'] ?? []);
    }

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
            'social' => $this->postsAdapter($response->json(), $instance->token)
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