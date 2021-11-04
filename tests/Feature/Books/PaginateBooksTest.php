<?php

namespace Tests\Feature\Books;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Book;
use Tests\TestCase;

class PaginateBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_paginated_books()
    {
        Book::factory()->times(10)->create();

        $url = route('api.v1.books.index', ['page[size]' => 2, 'page[number]' => 3]);

        $response = $this->jsonApi()->get($url);

        $response->assertJsonStructure([
           'links' => ['first', 'last', 'prev', 'next']
        ]);

        $response->assertJsonFragment([
            'first' => route('api.v1.books.index', ['page[number]' => 1, 'page[size]' => 2]),
            'last' => route('api.v1.books.index', ['page[number]' => 5, 'page[size]' => 2]),
            'prev' => route('api.v1.books.index', ['page[number]' => 2, 'page[size]' => 2]),
            'next' => route('api.v1.books.index', ['page[number]' => 4, 'page[size]' => 2]),
        ]);
    }
}
