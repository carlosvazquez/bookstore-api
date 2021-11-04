<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_logout()
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.v1.logout'))
            ->assertStatus(204)
        ;

        $this->assertNull(PersonalAccessToken::findToken($token));
    }
}
