<?php

namespace Tests\Feature\Authors;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_include_books()
    {
        $author = User::factory()
            ->hasBooks()
            ->create();

        $this->jsonApi()
            ->includePaths('books')
            ->get(route('api.v1.authors.read', $author))
            ->assertSee($author->books[0]->title)
            ->assertJsonFragment([
                'related' => route('api.v1.authors.relationships.books', $author)
            ])
            ->assertJsonFragment([
                'self' => route('api.v1.authors.relationships.books.read', $author)
            ])
        ;
    }

    /** @test */
    public function can_fetch_related_books()
    {
        $author = User::factory()
            ->hasBooks()
            ->create();

        $this->jsonApi()
            ->get(route('api.v1.authors.relationships.books', $author))
            ->assertSee($author->books[0]->title)
        ;

        $this->jsonApi()
            ->get(route('api.v1.authors.relationships.books.read', $author))
            ->assertSee($author->books[0]->getRouteKey())
        ;
    }
}
