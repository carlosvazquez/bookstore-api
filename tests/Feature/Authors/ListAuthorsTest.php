<?php

namespace Tests\Feature\Authors;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ListAuthorsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_fetch_single_authors()
    {
        $author = User::factory()->create();

        $response = $this->jsonApi()->get(route('api.v1.authors.read', $author))
            ->assertSee($author->name);

        $this->assertTrue(
            Str::isUuid($response->json('data.id')),
            "The authors 'id' must be Uuid."
        );
    }

    /** @test */
    public function can_fetch_all_authors()
    {
        $authors = User::factory()->times(3)->create();

        $this->jsonApi()->get(route('api.v1.authors.index'))
            ->assertSee($authors[0]->name);
    }
}
