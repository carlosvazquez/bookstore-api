<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncludeAuthorsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_authors()
    {
        $books = Book::factory()->create();

        $this->jsonApi()
            ->includePaths('authors')
            ->get(route('api.v1.books.read', $books))
            ->assertSee($books->user->name)
            ->assertJsonFragment([
                'related' => route('api.v1.books.relationships.authors', $books)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.books.relationships.authors.read', $books)
            ])
        ;
    }

    /** @test */
    public function can_fetch_related_authors()
    {
        $books = Book::factory()->create();

        $this->jsonApi()
            ->get(route('api.v1.books.relationships.authors', $books))
            ->assertSee($books->user->name)
        ;

        $this->jsonApi()
            ->get(route('api.v1.books.relationships.authors.read', $books))
            ->assertSee($books->user->id)
        ;
    }
}
