<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_single_category()
    {
        $category = Category::factory()->create();

        $response = $this->jsonApi()->get(route('api.v1.categories.read', $category));

        $response->assertJson([
            'data' => [
                'type' => 'categories',
                'id' => (string) $category->getRouteKey(),
                'attributes' => [
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'links' => [
                    'self' => route('api.v1.categories.read', $category)
                ]
            ]
        ]);
    }


    /** @test */
    public function can_fetch_all_categories()
    {
        $categories = Category::factory()->times(3)->create();

        $response = $this->jsonApi()->get(route('api.v1.categories.index'));

        $response->assertJsonFragment([
            'type' => 'categories',
            'id' => (string) $categories[0]->getRouteKey(),
            'attributes' => [
                'name' => $categories[0]->name,
                'slug' => $categories[0]->slug,
            ],
            'links' => [
                'self' => route('api.v1.categories.read', $categories[0])
            ]
        ]);

    }
}
