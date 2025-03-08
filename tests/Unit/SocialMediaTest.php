<?php

namespace Tests\Unit;

use App\Enums\SocialMediaOptions;
use App\Models\SocialMedia;
use App\Models\User;
use BenSampo\Enum\Exceptions\InvalidEnumMemberException;
use Illuminate\Database\UniqueConstraintViolationException;
use Tests\TestCase;

class SocialMediaTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create();
    }

    public function test_social_media_only_for_available_options(): void
    {
        SocialMedia::factory()->create([
            'type' => SocialMediaOptions::Facebook
        ]);

        $this->expectException(InvalidEnumMemberException::class);

        SocialMedia::factory()->create([
            'type' => 'orkut'
        ]);
    }

    public function test_token_is_not_visible_on_serialization(): void
    {
        $instance = SocialMedia::factory()->create();

        $this->assertArrayNotHasKey('token', $instance->toArray());
    }

    public function test_unique_constraint_violation(): void
    {
        $attrs = [
            'name' => 'Test',
        ];

        SocialMedia::factory()->create($attrs);

        $this->expectException(UniqueConstraintViolationException::class);

        SocialMedia::factory()->create($attrs);
    }
}
