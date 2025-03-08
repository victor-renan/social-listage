<?php

namespace Tests\Feature;

use App\Enums\SocialMediaOptions;
use App\Models\SocialMedia;
use App\Models\User;
use Tests\TestCase;

class SocialMediaControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create();
    }

    public function test_listing_all_social_medias(): void
    {
        SocialMedia::factory()->create(['type' => SocialMediaOptions::Facebook]);
        SocialMedia::factory()->create(['type' => SocialMediaOptions::Instagram]);
        SocialMedia::factory()->create(['type' => SocialMediaOptions::Linkedin]);

        $response = $this->get('/api/social');
        $response->assertJsonIsArray();
        $response->assertJsonCount(3);
        $response->assertStatus(200);

        $response = $this->get('/api/social?type=' . SocialMediaOptions::Facebook);
        $response->assertJsonIsArray();
        $response->assertJsonCount(1);
        $response->assertStatus(200);
    }

    public function test_details_for_social_media(): void
    {
        $instance = SocialMedia::factory()->create();

        $response = $this->get("/api/social/$instance->id");
        $response->assertStatus(200);
        $response->assertJsonIsObject();
        $response->assertJsonMissing(['token']);
    }

    public function test_posts_strategies_for_social_media(): void
    {
        $facebook = SocialMedia::factory()->create(['type' => SocialMediaOptions::Facebook]);
        $insta = SocialMedia::factory()->create(['type' => SocialMediaOptions::Instagram]);

        $faceRes = $this->get("/api/social/$facebook->id/posts");
        $instaRes = $this->get("/api/social/$insta->id/posts");

        $this->assertEquals($faceRes->json()['type'], SocialMediaOptions::Facebook);
        $this->assertEquals($instaRes->json()['type'], SocialMediaOptions::Instagram);
    }

    public function test_basic_case_creating_social_media(): void
    {
        $attrs = [
            'name' => 'Test',
            'type' => SocialMediaOptions::Facebook,
            'token' => fake()->text(),
            'user_id' => User::latest()->first()->id
        ];

        $response = $this->put("/api/social", $attrs);
        $response->assertStatus(200);
        $response = $this->put("/api/social", $attrs);
        $response->assertStatus(400);
    }

    public function test_additional_info_case_creating_social_media(): void
    {
        $response = $this->put("/api/social", [
            'name' => 'Test',
            'type' => SocialMediaOptions::Linkedin,
            'token' => fake()->text(),
            'user_id' => User::latest()->first()->id,
            'additional_info' => [
                'org_id' => "123123"
            ],
        ]);

        $response->assertStatus(200);

        $response = $this->put("/api/social", [
            'name' => 'Test2',
            'type' => SocialMediaOptions::Linkedin,
            'token' => fake()->text(),
            'user_id' => User::latest()->first()->id,
        ]);

        $response->assertStatus(400);

        $this->assertArrayHasKey('errors', $response->json());
        $this->assertArrayHasKey('additional_info.org_id', $response->json()['errors']);
    }

    public function test_update_social_media(): void
    {
        $instance = SocialMedia::factory()->create(['type' => SocialMediaOptions::Facebook]);

        $newName = 'Test2';

        $response = $this->patch("/api/social/$instance->id", [
            'name' => $newName,
        ]);

        $response->assertStatus(200);

        $this->assertEquals($response->json()['data']['name'], $newName);

        $response = $this->get("/api/social/$instance->id");

        $response->assertStatus(200);

        $this->assertEquals($response->json()['name'], $newName);
    }

    public function test_deletion_social_media(): void
    {
        $instance = SocialMedia::factory()->create(['type' => SocialMediaOptions::Facebook]);

        $this->assertTrue($instance->exists());

        $response = $this->delete("/api/social/$instance->id");

        $response->assertStatus(200);

        $this->assertFalse($instance->exists());
    }
}
