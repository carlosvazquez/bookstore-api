<?php

namespace Tests\Unit\Commands;

use App\Models\Permission;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GeneratePermissionsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_generate_permissions_for_registered_api_resources()
    {
        config(['json-api-v1.resources' => [
            'books' => \App\Models\Book::class,
        ]]);

        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

        foreach (Permission::$abilities as $ability) {
            $this->assertDatabaseHas('permissions', [
                'name' => "books:{$ability}"
            ]);
        }

        $this->artisan('generate:permissions')
            ->expectsOutput('Permissions generated!');

        $this->assertDatabaseCount('permissions', count(Permission::$abilities));
    }
}
