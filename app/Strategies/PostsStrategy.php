<?php

namespace App\Strategies;

use App\Models\SocialMedia;

interface PostsStrategy {
    public function getPosts(SocialMedia $instance): array;
}