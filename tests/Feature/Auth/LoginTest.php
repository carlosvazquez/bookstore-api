<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;
use Laravel\Sanctum\PersonalAccessToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_login_with_valid_credentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'iPhone de ' . $user->name
        ]);

        $token = $response->json('plain-text-token');

        $this->assertNotNull(
            PersonalAccessToken::findToken($token),
            "The plain text token is invalid"
        );
    }

    /** @test */
    public function user_permissions_are_assigned_as_abilities_to_the_token_response()
    {
        $user = User::factory()->create();

        $permission1 = Permission::factory()->create([
            'name' => $booksCreatePermission = 'books:create'
        ]);

        $permission2 = Permission::factory()->create([
            'name' => $booksUpdatePermission = 'books:update'
        ]);

        $user->givePermissionTo($permission1);
        $user->givePermissionTo($permission2);

        $response = $this->postJson(route('api.v1.login'), [
            'email' => $user->email,
            'password' => 'password',
            'device_name' => 'iPhone de ' . $user->name
        ]);

        $dbToken = PersonalAccessToken::findToken(
            $response->json('plain-text-token')
        );

        $this->assertTrue($dbToken->can($booksCreatePermission));
        $this->assertTrue($dbToken->can($booksUpdatePermission));
        $this->assertFalse($dbToken->can('books:delete'));
    }

    /** @test */
    public function cannot_login_with_invalid_credentials()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'jorge@aprendible.com',
            'password' => 'wrong-password',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('email');
    }

    /** @test */
    public function cannot_login_twice()
    {
        $user = User::factory()->create();

        $token = $user->createToken($user->name)->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson(route('api.v1.login'))
            ->assertStatus(204)
        ;
    }

    /** @test */
    public function email_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => '',
            'password' => 'wrong-password',
            'device_name' => 'iPhone de Jorge'
        ])->assertSee(__('validation.required', ['attribute' => 'email']))
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function email_must_be_valid()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'invalid-email',
            'password' => 'wrong-password',
            'device_name' => 'iPhone de Jorge'
        ])->assertSee(__('validation.email', ['attribute' => 'email']))
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function password_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'jorge@aprendible.com',
            'password' => '',
            'device_name' => 'iPhone de Jorge'
        ])->assertJsonValidationErrors('password');
    }

    /** @test */
    public function device_name_is_required()
    {
        $this->postJson(route('api.v1.login'), [
            'email' => 'jorge@aprendible.com',
            'password' => 'password',
            'device_name' => ''
        ])->assertJsonValidationErrors('device_name');
    }
}
