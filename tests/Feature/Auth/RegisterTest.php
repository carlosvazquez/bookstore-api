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
    public function can_register()
    {
        $response = $this->postJson(route('api.v1.register'), [
            'name' => 'John Doe',
            'email' => 'john@mail.com',
            'device_name' => 'Dispositivo de John',
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            "The plain text token is invalid"
        );

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@mail.com',
        ]);
    }

    /** @test */
    public function cannot_register_twice()
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson(route('api.v1.register'))
            ->assertStatus(204);
    }

    /** @test */
    public function name_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => '',
            'email' => 'john@mail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de John'
        ])->assertStatus(422);
    }

    /** @test */
    public function email_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de John'
        ])->assertStatus(422);
    }

    /** @test */
    public function email_must_be_valid()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => 'invalid-email',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de John'
        ])->assertStatus(422);
    }

    /** @test */
    public function email_must_be_unique()
    {
        $user = User::factory()->create();

        $this->postJson(route('api.v1.register'), [
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de John'
        ])->assertStatus(422);
    }

    /** @test */
    public function password_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => 'john@mail.com',
            'password' => '',
            'password_confirmation' => 'password',
            'device_name' => 'iPhone de John'
        ])->assertStatus(422);
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $this->postJson(route('api.v1.register'), [
            'email' => 'john@mail.com',
            'password' => 'password',
            'password_confirmation' => 'not-confirmed',
            'device_name' => 'iPhone de John'
        ])->assertStatus(422);
    }
    /** @test */
    public function device_name_is_required()
    {
        $this->postJson(route('api.v1.register'), [
            'name' => 'john',
            'email' => 'john@mail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'device_name' => ''
        ])->assertStatus(422);
    }
}
