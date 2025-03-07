<?php

namespace Database\Seeders;

use App\Enums\SocialMediaOptions;
use App\Models\SocialMedia;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create();

        SocialMedia::factory()->create([
            'name' => SocialMediaOptions::Facebook,
            'token' => env('FACEBOOK_TOKEN')
        ]);

        SocialMedia::factory()->create([
            'name' => SocialMediaOptions::Instagram,
            'token' => env('INSTA_TOKEN')
        ]);
    }
}
