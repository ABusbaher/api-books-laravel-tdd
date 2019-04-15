<?php

namespace Tests\Unit;

use App\Author;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthorTest extends TestCase
{
    use RefreshDatabase;

    /**
     *@test
     */
    public function it_returns_author_id_either_author_is_just_created_or_already_exists()
    {
        $author = factory('App\Author')->create(['author' => 'John']);
        $request = Author::findOrCreateAuthor('John');
        $this->assertEquals($request, $author->id);

        $request2 = Author::findOrCreateAuthor('Jolly');
        $this->assertNotEquals($request2, $author->id);
        $this->assertEquals($request2, 2);
    }
}
