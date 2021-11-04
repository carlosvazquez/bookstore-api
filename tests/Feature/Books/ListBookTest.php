<?php

namespace Tests\Feature\Books;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListBookTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_single_book()
    {
        $book = Book::factory()->create();

        $response = $this->jsonApi()->get(route('api.v1.books.read', $book));

        $response->assertJson([
            'data' => [
                'type' => 'books',
                'id' => (string) $book->getRouteKey(),
                'attributes' => [
                    'title' => $book->title,
                    'slug' => $book->slug,
                    'content' => $book->content,
                    'created-at' => $book->created_at->toAtomString(),
                    'updated-at' => $book->updated_at->toAtomString(),
                ],
                'links' => [
                    'self' => route('api.v1.books.read', $book)
                ]
            ]
        ]);

        $this->assertNull(
            $response->json('data.relationships.authors.data'),
            "The key 'data.relationships.authors.data' must be null"
        );
    }

    /** @test */
    public function can_fetch_all_books()
    {
        $books = Book::factory()->times(3)->create();

        $response = $this->jsonApi()->get(route('api.v1.books.index'));

        $response->assertJson([
            'data' => [
                [
                    'type' => 'books',
                    'id' => (string) $books[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $books[0]->title,
                        'slug' => $books[0]->slug,
                        'content' => $books[0]->content,
                        'created-at' => $books[0]->created_at->toAtomString(),
                        'updated-at' => $books[0]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.books.read', $books[0])
                    ]
                ],
                [
                    'type' => 'books',
                    'id' => (string) $books[1]->getRouteKey(),
                    'attributes' => [
                        'title' => $books[1]->title,
                        'slug' => $books[1]->slug,
                        'content' => $books[1]->content,
                        'created-at' => $books[1]->created_at->toAtomString(),
                        'updated-at' => $books[1]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.books.read', $books[1])
                    ]
                ],
                [
                    'type' => 'books',
                    'id' => (string) $books[2]->getRouteKey(),
                    'attributes' => [
                        'title' => $books[2]->title,
                        'slug' => $books[2]->slug,
                        'content' => $books[2]->content,
                        'created-at' => $books[2]->created_at->toAtomString(),
                        'updated-at' => $books[2]->updated_at->toAtomString(),
                    ],
                    'links' => [
                        'self' => route('api.v1.books.read', $books[2])
                    ]
                ],
            ]
        ]);
    }
}
