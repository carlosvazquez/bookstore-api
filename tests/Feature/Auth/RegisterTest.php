<?php

namespace Tests\Feature\Auth;

use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_register()
    {
        $response = $this->postJson(route('api.v1.register'), [
            'name' => 'Jorge García',
            'email' => 'jorge@aprendible.com',
            'device_name' => 'Dispositivo de Jorge',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            "The plain text token is invalid"
        );

        $this->assertDatabaseHas('users', [
            'name' => 'Jorge García',
            'email' => 'jorge@aprendible.com',
        ]);
    }

    /** @test */
    function cannot_register_twice()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.register'))
            ->assertStatus(204);
    }

    /** @test */
    function name_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => '',
            'email' => 'jorge@aprendible.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('name');
    }

    /** @test */
    function email_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    function email_must_be_valid()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    function email_must_be_unique()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'), [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    function password_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => 'jorge@aprendible.com',
            'password' => '',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('password');
    }

    /** @test */
    function password_must_be_confirmed()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => 'jorge@aprendible.com',
            'password' => 'password',
            'password_confirmation' => 'not-confirmed',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('password');
    }
    /** @test */
    function device_name_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => 'jorge@aprendible.com',
            'password' => 'password',
            'device_name' => ''
        ])->assertJsonValidationErrors('device_name');
    }

}
