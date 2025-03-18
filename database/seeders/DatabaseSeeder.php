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
            'type' => SocialMediaOptions::Facebook,
            'name' => 'Facebooka',
            'token' => env('FACEBOOK_TOKEN')
        ]);

        SocialMedia::factory()->create([
            'type' => SocialMediaOptions::Instagram,
            'name' => 'Instagrams',
            'token' => env('INSTA_TOKEN')
        ]);

        SocialMedia::factory()->create([
            'type' => SocialMediaOptions::Linkedin,
            'name' => 'Linkedidn',
            'token' => env('LINKEDIN_TOKEN'),
            'additional_info' => [
                'org_id' => '106668152'
            ],
        ]);
    }
}
