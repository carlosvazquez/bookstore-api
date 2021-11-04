<?php

namespace Tests\Feature\Books;

use App\Models\Category;
use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_update_books()
    {
        $book = Book::factory()->create();

        $this->jsonApi()
            ->patch(route('api.v1.books.update', $book))
            ->assertStatus(401)
        ;
    }

    /** @test */
    public function authenticated_users_can_update_their_books()
    {
        $book = Book::factory()->create();

        $category = Category::factory()->create();

        Sanctum::actingAs($user = $book->user, ['books:update']);

        $this->jsonApi()
            ->withData([
                'type' => 'books',
                'id' => $book->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
                    'slug' => 'title-changed',
                    'content' => 'Content changed',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            'id' => $user->getRouteKey(),
                            'type' => 'authors'
                        ]
                    ],
                    'categories' => [
                        'data' => [
                            'id' => $category->getRouteKey(),
                            'type' => 'categories'
                        ]
                    ]
                ]
            ])
            ->patch(route('api.v1.books.update', $book))
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('books', [
            'title' => 'Title changed',
            'slug' => 'title-changed',
            'content' => 'Content changed',
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_update_their_books_without_permissions()
    {
        $book = Book::factory()->create();

        $category = Category::factory()->create();

        Sanctum::actingAs($user = $book->user);

        $this->jsonApi()
            ->withData([
                'type' => 'books',
                'id' => $book->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
                    'slug' => 'title-changed',
                    'content' => 'Content changed',
                ],
                'relationships' => [
                    'authors' => [
                        'data' => [
                            'id' => $user->getRouteKey(),
                            'type' => 'authors'
                        ]
                    ],
                    'categories' => [
                        'data' => [
                            'id' => $category->getRouteKey(),
                            'type' => 'categories'
                        ]
                    ]
                ]
            ])
            ->patch(route('api.v1.books.update', $book))
            ->assertStatus(403)
        ;

        $this->assertDatabaseMissing('books', [
            'title' => 'Title changed',
            'slug' => 'title-changed',
            'content' => 'Content changed',
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_update_others_books()
    {
        $book = Book::factory()->create();

        Sanctum::actingAs($user = User::factory()->create());

        $this->jsonApi()
            ->withData([
                'type' => 'books',
                'id' => $book->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
                    'slug' => 'title-changed',
                    'content' => 'Content changed',
                ]
            ])
            ->patch(route('api.v1.books.update', $book))
            ->assertStatus(403)
        ;

        $this->assertDatabaseMissing('books', [
            'title' => 'Title changed',
            'slug' => 'title-changed',
            'content' => 'Content changed',
        ]);
    }

    /** @test */
    public function can_update_the_title_only()
    {
        $book = Book::factory()->create();

        Sanctum::actingAs($book->user, ['books:update']);

        $this->jsonApi()
            ->withData([
                'type' => 'books',
                'id' => $book->getRouteKey(),
                'attributes' => [
                    'title' => 'Title changed',
                ]
            ])
            ->patch(route('api.v1.books.update', $book))
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('books', [
            'title' => 'Title changed',
        ]);
    }

    /** @test */
    public function can_update_the_slug_only()
    {
        $book = Book::factory()->create();

        Sanctum::actingAs($book->user, ['books:update']);

        $this->jsonApi()
            ->withData([
                'type' => 'books',
                'id' => $book->getRouteKey(),
                'attributes' => [
                    'slug' => 'slug-changed',
                ]
            ])
            ->patch(route('api.v1.books.update', $book))
            ->assertStatus(200)
        ;

        $this->assertDatabaseHas('books', [
            'slug' => 'slug-changed',
        ]);
    }

    /** @test */
    public function can_replace_the_categories()
    {
        $book = Book::factory()->create();
        $category = Category::factory()->create();

        Sanctum::actingAs($book->user, ['books:modify-categories']);

        $this->jsonApi()
            ->withData([
                'type' => 'categories',
                'id' => $category->getRouteKey(),
            ])
            ->patch(route('api.v1.books.relationships.categories.replace', $book))
            ->assertStatus(204)
        ;

        $this->assertDatabaseHas('books', [
            'category_id' => $category->id,
        ]);
    }

    /** @test */
    public function can_replace_the_author()
    {
        $book = Book::factory()->create();
        $author = User::factory()->create();

        Sanctum::actingAs($book->user, ['books:modify-authors']);

        $this->jsonApi()
            ->withData([
                'type' => 'authors',
                'id' => $author->getRouteKey(),
            ])
            ->patch(route('api.v1.books.relationships.authors.replace', $book))
            ->assertStatus(204)
        ;

        $this->assertDatabaseHas('books', [
            'user_id' => $author->id,
        ]);
    }
}
