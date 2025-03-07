<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Http;

class InstagramPostsStrategy implements PostsStrategy
{
    protected string $apiUrl = 'https://graph.instagram.com/v22.0/me/media';

    public function getPosts(SocialMedia $instance): array
    {
        return Http::get($this->apiUrl, [
            'access_token' => $instance->token,
            'fields' => 'fields=id,thumbnail_url,media_type,media_url,username,owner{name,username},timestamp,like_count,is_shared_to_feed,comments_count,caption',
        ])->json();
    }
}