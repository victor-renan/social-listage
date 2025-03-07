<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Http;

class FacebookPostsStrategy implements PostsStrategy
{
    protected string $apiUrl = 'https://graph.facebook.com/v22.0/me/posts';

    public function getPosts(SocialMedia $instance): array
    {
        $response = Http::get($this->apiUrl, [
            'access_token' => $instance->token,
            'fields' => 'actions,attachments{description,media,media_type,title,subattachments,description_tags,target,type,unshimmed_url,url},caption,is_hidden,is_expired,message,from,place,status_type,reactions,created_time',
        ]);

        return $response->json()['data'];
    }
}