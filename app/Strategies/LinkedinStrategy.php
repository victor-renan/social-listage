<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use Http;

class LinkedinStrategy implements SocialMediaStrategy
{
    protected string $apiUrl = 'https://api.linkedin.com/rest/posts';
    protected string $imageUrl = 'https://api.linkedin.com/rest/images';
    protected string $videoUrl = 'https://api.linkedin.com/rest/videos';
    protected int $count = 4;

    private function filter(array $item): bool
    {
        return isset($item['content']['media']) || isset($item['content']['multiImage']);
    }

    private function fetchImageUrl(array $item, array $headers)
    {
        if (isset($item['content']['multiImage'])) {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->get("$this->imageUrl/" . $item['content']['multiImage']['images'][0]['id']);
            return $response->ok() ? $response->json()['downloadUrl'] : null;
        }

        if (isset($item['content']['media']) && str_contains($item['content']['media']['id'], ':image:')) {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->get("$this->imageUrl/" . $item['content']['media']['id']);
            return $response->ok() ? $response->json()['downloadUrl'] : null;
        }

        if (isset($item['content']['media']) && str_contains($item['content']['media']['id'], ':video:')) {
            $response = Http::withHeaders($headers)
                ->timeout(5)
                ->get("$this->videoUrl/" . $item['content']['media']['id']);
            return $response->ok() ? $response->json()['thumbnail'] : null;
        }

        return null;
    }

    private function fetchPostUrl(array $item)
    {
        return 'https://linkedin.com/feed/update/' . $item['id'];
    }

    protected function postsAdapter(array $json, array $headers): array
    {
        $filtered = [];

        foreach ($json['elements'] as $post) {
            if ($this->filter($post)) {
                array_push($filtered, [
                    'url' => $this->fetchPostUrl($post),
                    'image_url' => $this->fetchImageUrl($post, $headers)
                ]);

                if (count($filtered) == $this->count) {
                    break;
                }
            }
        }

        return $filtered;
    }

    public function posts(SocialMedia $instance): JsonResponse
    {
        $c = (object) $instance->additional_info;
        $orgId = $c?->org_id;

        if (!$orgId) {
            return response()->json([
                'type' => $instance->type,
                'social' => [
                    'message' => 'Atualize esta implementação adicionando o ID da empresa do Linkedin'
                ],
            ], 400);
        }

        $headers = [
            'Authorization' => "Bearer $instance->token",
            'LinkedIn-Version' => '202502',
        ];

        $params = [
            'q' => 'author',
            'author' => "urn:li:organization:$orgId",
        ];

        $response = Http::withHeaders($headers)
            ->timeout(5)
            ->withQueryParameters($params)
            ->get($this->apiUrl);

        return response()->json([
            'type' => $instance->type,
            'posts' => $this->postsAdapter($response->json(), $headers)
        ], $response->status());
    }

    public function validate(array $data): Validator
    {
        $common = SocialMedia::validation($data);

        return \Validator::make(
            $data,
            array_merge($common->rules, [
                'additional_info.org_id' => 'required|string'
            ]),
            array_merge($common->messages, [
                'additional_info.org_id.required' => 'Digite o org_id da página do Linkedin'
            ])
        );
    }
}