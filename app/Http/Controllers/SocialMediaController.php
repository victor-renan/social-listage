<?php

namespace App\Http\Controllers;

use App\Enums\SocialMediaOptions;
use App\Models\SocialMedia;
use App\Strategies\FacebookPostsStrategy;
use App\Strategies\PostsStrategy;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    public ?PostsStrategy $strategy = null;

    public function setIntegrationStrategy(?PostsStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function posts(Request $request)
    {
        if (!$request->type) {
            return response()->json([ 
                'message' => 'Escolha a plataforma desejada'
            ], 404);
        }

        $instance = SocialMedia::where(['name' => $request->type])->first();

        if (!$instance) {
            return response()->json([
                'message' => 'Plataforma não disponível'
            ], 404);
        }

        match ($request->type) {
            SocialMediaOptions::Facebook->value => $this->setIntegrationStrategy(new FacebookPostsStrategy()),
            SocialMediaOptions::Instagram->value => null,
            SocialMediaOptions::Linkedin->value => null,
            default => null
        };

        if (!$this->strategy) {
            return response()->json([
                'message' => 'Estratégia de integração não encontrada'
            ], 500);
        }

        $posts = $this->strategy->getPosts($instance);

        return response()->json($posts);
    }
}
