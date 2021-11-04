<?php

namespace Tests\Feature\Books;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Book;
use Tests\TestCase;

class SortBooksTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_sort_books_by_title_asc()
    {
        Book::factory()->create(['title' => 'C Title']);
        Book::factory()->create(['title' => 'A Title']);
        Book::factory()->create(['title' => 'B Title']);

        $url = route('api.v1.books.index', ['sort' => 'title']);

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'A Title',
            'B Title',
            'C Title',
        ]);
    }

    /** @test */
    public function it_can_sort_books_by_title_desc()
    {
        Book::factory()->create(['title' => 'C Title']);
        Book::factory()->create(['title' => 'A Title']);
        Book::factory()->create(['title' => 'B Title']);

        $url = route('api.v1.books.index', ['sort' => '-title']);

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'C Title',
            'B Title',
            'A Title',
        ]);
    }

    /** @test */
    public function it_can_sort_books_by_title_and_content()
    {
        Book::factory()->create([
            'title' => 'C Title',
            'content' => 'B content'
        ]);
        Book::factory()->create([
            'title' => 'A Title',
            'content' => 'C content'
        ]);
        Book::factory()->create([
            'title' => 'B Title',
            'content' => 'D content'
        ]);

        $url = route('api.v1.books.index').'?sort=title,-content';

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'A Title',
            'B Title',
            'C Title',
        ]);

        $url = route('api.v1.books.index').'?sort=-content,title';

        $this->jsonApi()->get($url)->assertSeeInOrder([
            'D content',
            'C content',
            'B content',
        ]);
    }

    /** @test */
    public function it_cannot_sort_books_by_unknown_fields()
    {
        Book::factory()->times(3)->create();

        $url = route('api.v1.books.index').'?sort=unknown';

        $this->jsonApi()->get($url)->assertStatus(400);
    }
}
