<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_delete_categories()
    {
        $category = Category::factory()->create();

        $this->jsonApi()
            ->delete(route('api.v1.categories.delete', $category))
            ->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_delete_categories()
    {
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()
            ->delete(route('api.v1.categories.delete', $category))
            ->assertStatus(204);
    }
}
