<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Permission;

class UserPermissionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_assign_permissions_to_a_user()
    {
        $user = User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }
    /** @test */
    function cannot_assign_the_same_permission_twice()
    {
        $user = User::factory()->create();

        $permission = Permission::factory()->create();

        $user->givePermissionTo($permission);

        $user->givePermissionTo($permission);

        $this->assertCount(1, $user->fresh()->permissions);
    }
}
