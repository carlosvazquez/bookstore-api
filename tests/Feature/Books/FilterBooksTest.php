<?php

namespace Tests\Feature\Books;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Book;
use Tests\TestCase;

class FilterBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_books_by_title()
    {
        Book::factory()->create([
            'title' => 'Aprende Laravel Desde Cero'
        ]);

        Book::factory()->create([
            'title' => 'Other Book'
        ]);

        $url = route('api.v1.books.index', ['filter[title]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel Desde Cero')
            ->assertDontSee('Other Book')
        ;
    }

    /** @test */
    public function can_filter_books_by_content()
    {
        Book::factory()->create([
            'content' => '<div>Aprende Laravel Desde Cero</div>'
        ]);

        Book::factory()->create([
            'content' => '<div>Other Book</div>'
        ]);

        $url = route('api.v1.books.index', ['filter[content]' => 'Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Aprende Laravel Desde Cero')
            ->assertDontSee('Other Book')
        ;
    }

    /** @test */
    public function can_filter_books_by_year()
    {
        Book::factory()->create([
            'title' => 'Book from 2020',
            'created_at' => now()->year(2020)
        ]);

        Book::factory()->create([
            'title' => 'Book from 2021',
            'created_at' => now()->year(2021)
        ]);

        $url = route('api.v1.books.index', ['filter[year]' => 2020]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(1, 'data')
            ->assertSee('Book from 2020')
            ->assertDontSee('Book from 2021')
        ;
    }

    /** @test */
    public function can_filter_books_by_month()
    {
        Book::factory()->create([
            'title' => 'Book from February',
            'created_at' => now()->month(3)
        ]);
        Book::factory()->create([
            'title' => 'Another Book from February',
            'created_at' => now()->month(3)
        ]);

        Book::factory()->create([
            'title' => 'Book from January',
            'created_at' => now()->month(1)
        ]);

        $url = route('api.v1.books.index', ['filter[month]' => 3]);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Book from February')
            ->assertSee('Another Book from February')
            ->assertDontSee('Book from January')
        ;
    }

    /** @test */
    public function cannot_filter_books_by_unknown_filters()
    {
        Book::factory()->create();

        $url = route('api.v1.books.index', ['filter[unknown]' => 2]);

        $this->jsonApi()->get($url)->assertStatus(400);
    }

    /** @test */
    public function can_search_books_by_title_and_content()
    {
        Book::factory()->create([
            'title' => 'Book from Bookstore',
            'content' => 'Content'
        ]);
        Book::factory()->create([
            'title' => 'Another Book',
            'content' => 'Content Bookstore...'
        ]);

        Book::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);

        $url = route('api.v1.books.index', ['filter[search]' => 'Bookstore']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(2, 'data')
            ->assertSee('Book from Bookstore')
            ->assertSee('Another Book')
            ->assertDontSee('Title 2')
        ;
    }

    /** @test */
    public function can_search_books_by_title_and_content_with_multiple_terms()
    {
        Book::factory()->create([
            'title' => 'Book from Bookstore',
            'content' => 'Content'
        ]);

        Book::factory()->create([
            'title' => 'Another Book',
            'content' => 'Content Bookstore...'
        ]);

        Book::factory()->create([
            'title' => 'Another Laravel Book',
            'content' => 'Content...'
        ]);

        Book::factory()->create([
            'title' => 'Title 2',
            'content' => 'content 2'
        ]);

        $url = route('api.v1.books.index', ['filter[search]' => 'Bookstore Laravel']);

        $this->jsonApi()->get($url)
            ->assertJsonCount(3, 'data')
            ->assertSee('Book from Bookstore')
            ->assertSee('Another Book')
            ->assertSee('Another Laravel Book')
            ->assertDontSee('Title 2')
        ;
    }

    /** @test */
    public function can_filter_books_by_category()
    {
        Book::factory()->count(2)->create();

        $category = Category::factory()->hasbooks(2)->create();

        $this->jsonApi()
            ->filter(['categories' => $category->getRouteKey()])
            ->get(route('api.v1.books.index'))
            ->assertJsonCount(2, 'data')
        ;
    }

    /** @test */
    public function can_filter_books_by_multiple_categories()
    {
        Book::factory()->count(2)->create();

        $category = Category::factory()->hasbooks(2)->create();
        $category2 = Category::factory()->hasbooks(3)->create();

        $this->jsonApi()
            ->filter([
                'categories' => $category->getRouteKey().','.$category2->getRouteKey()
            ])
            ->get(route('api.v1.books.index'))
            ->assertJsonCount(5, 'data')
        ;
    }

    /** @test */
    public function can_filter_books_by_authors()
    {
        $author = User::factory()->hasbooks(2)->create();

        Book::factory()->count(2)->create();

        $this->jsonApi()
            ->filter(['authors' => $author->name])
            ->get(route('api.v1.books.index'))
            ->assertJsonCount(2, 'data')
        ;
    }

    /** @test */
    public function can_filter_books_by_multiple_authors()
    {
        $author = User::factory()->hasbooks(2)->create();
        $author2 = User::factory()->hasbooks(3)->create();

        Book::factory()->count(2)->create();

        $this->jsonApi()
            ->filter([
                'authors' => $author->name.','.$author2->name
            ])
            ->get(route('api.v1.books.index'))
            ->assertJsonCount(5, 'data')
        ;
    }
}
