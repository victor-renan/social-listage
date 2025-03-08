<?php

namespace Database\Factories;

use App\Enums\SocialMediaOptions;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialMedia>
 */
class SocialMediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'type' => array_rand(SocialMediaOptions::asArray()),
            'token' => Str::random(50),
            'user_id' => User::latest()->first()->id
        ];
    }
}
