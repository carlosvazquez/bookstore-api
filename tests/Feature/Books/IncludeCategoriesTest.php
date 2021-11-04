<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeCategoriesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_categories()
    {
        $book = Book::factory()->create();

        $this->jsonApi()
            ->includePaths('categories')
            ->get(route('api.v1.books.read', $book))
            ->assertSee($book->category->name)
            ->assertJsonFragment([
                'related' => route('api.v1.books.relationships.categories', $book)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.books.relationships.categories.read', $book)
            ])
        ;
    }

    /** @test */
    public function can_fetch_related_categories()
    {
        $book = Book::factory()->create();

        $this->jsonApi()
            ->get(route('api.v1.books.relationships.categories', $book))
            ->assertSee($book->category->name)
        ;

        $this->jsonApi()
            ->get(route('api.v1.books.relationships.categories.read', $book))
            ->assertSee($book->category->getRouteKey())
        ;
    }
}
