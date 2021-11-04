<?php

namespace Tests\Feature\Books;

use App\Models\Category;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CreatebooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_users_cannot_create_books()
    {
        $book = array_filter(Book::factory()->raw(['user_id' => null]));

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))->assertStatus(401);

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function authenticated_users_can_create_books()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $book = array_filter(Book::factory()->raw([
            'category_id' => null,
            'approved' => true // mass assigment check
        ]));

        $this->assertDatabaseMissing('books', $book);

        Sanctum::actingAs($user, ['books:create']);

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book,
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
        ])->post(route('api.v1.books.create'))
            ->assertCreated();

        $this->assertDatabaseHas('books', [
            'user_id' => $user->id,
            'title' => $book['title'],
            'slug' => $book['slug'],
            'content' => $book['content'],
        ]);
    }

    /** @test */
    public function authenticated_users_cannot_create_books_without_permissions()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $book = Book::factory()->raw();

        Sanctum::actingAs($user);

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book,
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
        ])->post(route('api.v1.books.create'))
            ->assertStatus(403);

        $this->assertDatabaseCount('books', 0);
    }

    /** @test */
    public function authenticated_users_cannot_create_books_on_behalf_of_another_user()
    {
        $user = User::factory()->create();

        $category = Category::factory()->create();

        $book = array_filter(Book::factory()->raw([
            'category_id' => null,
            'user_id' => null,
        ]));

        $this->assertDatabaseMissing('books', $book);

        Sanctum::actingAs($user, ['books:create']);

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book,
            'relationships' => [
                'authors' => [
                    'data' => [
                        'id' => User::factory()->create()->getRouteKey(),
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
        ])->post(route('api.v1.books.create'))
            ->assertStatus(403);

        $this->assertDatabaseCount('books', 0);
    }

    /** @test */
    public function authors_is_required()
    {
        $book = Book::factory()->raw();
        $category = Category::factory()->create();

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => '/data']])
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function authors_must_be_a_relationship_object()
    {
        $book = Book::factory()->raw();
        $category = Category::factory()->create();

        $book['authors'] = 'id';

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book,
            'relationships' => [
                'categories' => [
                    'data' => [
                        'id' => $category->getRouteKey(),
                        'type' => 'categories'
                    ]
                ]
            ]
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/authors')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function categories_is_required()
    {
        $book = Book::factory()->raw(['category_id' => null]);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertJsonFragment(['source' => ['pointer' => '/data']])
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function categories_must_be_a_relationship_object()
    {
        $book = Book::factory()->raw(['category_id' => null]);

        $book['categories'] = 'slug';

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/categories')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function title_is_required()
    {
        $book = Book::factory()->raw(['title' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/title')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function content_is_required()
    {
        $book = Book::factory()->raw(['content' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/content')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function slug_is_required()
    {
        $book = Book::factory()->raw(['slug' => '']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function slug_must_be_unique()
    {
        Book::factory()->create(['slug' => 'same-slug']);

        $book = Book::factory()->raw(['slug' => 'same-slug']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function slug_must_must_only_contain_letters_numbers_and_dashes()
    {
        $book = Book::factory()->raw(['slug' => '#$^^%$']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function slug_must_must_not_contain_underscores()
    {
        $book = Book::factory()->raw(['slug' => 'with_underscores']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertSee(trans('validation.no_underscores', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function slug_must_must_not_start_with_dashes()
    {
        $book = Book::factory()->raw(['slug' => '-starts-with-dash']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertSee(trans('validation.no_starting_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('books', $book);
    }

    /** @test */
    public function slug_must_must_not_end_with_dashes()
    {
        $book = Book::factory()->raw(['slug' => 'ends-with-dash-']);

        Sanctum::actingAs(User::factory()->create());

        $this->jsonApi()->withData([
            'type' => 'books',
            'attributes' => $book
        ])->post(route('api.v1.books.create'))
            ->assertSee(trans('validation.no_ending_dashes', ['attribute' => 'slug']))
            ->assertStatus(422)
            ->assertSee('data\/attributes\/slug')
        ;

        $this->assertDatabaseMissing('books', $book);
    }
}
