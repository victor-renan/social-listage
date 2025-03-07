<?php

namespace App\Strategies;

use App\Models\SocialMedia;
use Http;

class InstagramPostsStrategy implements PostsStrategy
{
    protected string $apiUrl = ''; // Todo

    public function getPosts(SocialMedia $instance): array
    {
        // Todo
        return [];
    }
}