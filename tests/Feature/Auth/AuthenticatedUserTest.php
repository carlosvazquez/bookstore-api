<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthenticatedUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_fetch_the_authenticated_user()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->getJson(route('api.v1.user'))
            ->assertJson([
                'email' => $user->email
            ]);
    }

    /** @test */
    function guests_cannot_fetch_any_user()
    {
        $this->getJson(route('api.v1.user'))
            ->assertStatus(401);
    }
}
