<?php

namespace Tests\Feature\Categories;

use Tests\TestCase;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_books()
    {
        $category = Category::factory()
            ->hasBooks()
            ->create();

        $this->jsonApi()
            ->includePaths('books')
            ->get(route('api.v1.categories.read', $category))
            ->assertSee($category->books[0]->title)
            ->assertJsonFragment([
                'related' => route('api.v1.categories.relationships.books', $category)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.categories.relationships.books.read', $category)
            ])
        ;
    }

    /** @test */
    public function can_fetch_related_books()
    {
        $category = Category::factory()
            ->hasBooks()
            ->create();

        $this->jsonApi()
            ->get(route('api.v1.categories.relationships.books', $category))
            ->assertSee($category->books[0]->title)
        ;

        $this->jsonApi()
            ->get(route('api.v1.categories.relationships.books.read', $category))
            ->assertSee($category->books[0]->getRouteKey())
        ;
    }
}
