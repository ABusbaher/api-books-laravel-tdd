<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SearchAndGetBooksTest extends TestCase
{
    use RefreshDatabase;

    /**
    *@test
    */
    public function user_can_get_single_book_with_valid_slug()
    {
        $book = factory('App\Book')->create();

        $this->json('GET', "api/books/{$book->slug}")
            ->assertStatus(200)
            ->assertJson(['data'=>[
                'title' => $book->title,
                'year_of_publish' => $book->year_of_publish,
                'language' => $book->language->language,
                'original_language' => $book->original_language->language,
                'author' => $book->author->author]
            ]);
    }

    /**
    *@test
    */
    public function if_bad_book_slug_provided_404_returned()
    {
        factory('App\Book')->create(['slug'=> 'some-text']);

        $this->json('GET', "api/books/not-valid-slug")
            ->assertStatus(404);
    }

    /**
    *@test
    */
    public function user_can_get_all_books_paginated_five_per_page()
    {
        factory('App\Book',12)->create();
        $this->json('GET', "api/books/")->assertStatus(200);

        $response2 = $this->json('GET', "api/books?page=2");
        $responseArray = json_decode($response2->getContent());
        // assert the second page returned the 5 additional data
        $this->assertEquals(count($responseArray->data), 5);

        $response3 = $this->json('GET', "api/books?page=3");
        $responseArray = json_decode($response3->getContent());
        // assert the third page returned the 2 additional data
        $this->assertEquals(count($responseArray->data), 2);
    }

    /**
    *@test
    */
    public function users_can_search_books_by_author_name()
    {
        $this->withoutExceptionHandling();
        $author1 = factory('App\Author')->create(['author' =>'Ivo Andrić']);
        $author2 = factory('App\Author')->create(['author' =>'Miša Selimović']);

        factory('App\Book')->create(['author_id' => $author1->id]);
        factory('App\Book')->create(['author_id' => $author2->id]);
        factory('App\Book')->create(['author_id' => $author1->id]);

        $response = $this->json('GET', "api/books?search=Ivo")
            ->assertStatus(200);
        $responseArray = json_decode($response->getContent());
        $this->assertEquals(count($responseArray->data), 2);

        $response2 = $this->json('GET', "api/books?search=Sel")
            ->assertStatus(200);
        $responseArray = json_decode($response2->getContent());
        $this->assertEquals(count($responseArray->data), 1);

        $response3 = $this->json('GET', "api/books?search=NO RESULT SEARCH")
            ->assertStatus(200);
        $responseArray = json_decode($response3->getContent());
        $this->assertEquals(count($responseArray->data), 0);
    }

    /**
    *@test
    */
    public function usets_can_search_books_by_year_of_publish()
    {
        factory('App\Book')->create(['year_of_publish' => 1947]);
        factory('App\Book')->create(['year_of_publish' => 1942]);
        factory('App\Book')->create(['year_of_publish' => 1987]);

        $response = $this->json('GET', "api/books?search=194")
            ->assertStatus(200);
        $responseArray = json_decode($response->getContent());
        $this->assertEquals(count($responseArray->data), 2);

        $response = $this->json('GET', "api/books?search=19")
            ->assertStatus(200);
        $responseArray = json_decode($response->getContent());
        $this->assertEquals(count($responseArray->data), 3);

    }
}
