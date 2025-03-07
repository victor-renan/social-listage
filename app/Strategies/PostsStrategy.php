<?php

namespace App\Strategies;

use App\Models\SocialMedia;

interface PostsStrategy
{
    /**
     * Retrieves an array of posts from the specified social media instance.
     *
     * @param SocialMedia $instance The social media instance to retrieve posts from.
     * @return array An array of posts, based on each registered platform external API
     */
    public function getPosts(SocialMedia $instance): array;
}