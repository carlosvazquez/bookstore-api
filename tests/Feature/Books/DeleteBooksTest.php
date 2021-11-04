<?php

namespace Tests\Feature\Books;

use Tests\TestCase;
use App\Models\User;
use App\Models\Book;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_delete_books()
    {
        $book = Book::factory()->create();

        $this->jsonApi()
            ->delete(route('api.v1.books.delete', $book))
            ->assertStatus(401);
    }

    /** @test */
    public function authenticated_users_can_delete_their_books()
    {
        $book = Book::factory()->create();

        Sanctum::actingAs($book->user, ['books:delete']);

        $this->jsonApi()
            ->delete(route('api.v1.books.delete', $book))
            ->assertStatus(204);
    }

    /** @test */
    public function authenticated_users_cannot_delete_their_books_without_permissions()
    {
        $book = Book::factory()->create();

        Sanctum::actingAs($book->user);

        $this->jsonApi()
            ->delete(route('api.v1.books.delete', $book))
            ->assertStatus(403);
    }

    /** @test */
    public function authenticated_users_cannot_delete_others_books()
    {
        $book = Book::factory()->create();

        Sanctum::actingAs($user = User::factory()->create());

        $this->jsonApi()
            ->delete(route('api.v1.books.delete', $book))
            ->assertStatus(403);
    }
}
