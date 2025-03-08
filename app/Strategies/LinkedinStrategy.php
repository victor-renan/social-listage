<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Validator;
use Http;

class LinkedinStrategy implements SocialMediaStrategy
{
    protected string $apiUrl = 'https://api.linkedin.com/rest/posts';

    public function posts(SocialMedia $instance): JsonResponse
    {
        $orgId = $instance->additional_info?->org_id;

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
            'social' => $response->json()
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