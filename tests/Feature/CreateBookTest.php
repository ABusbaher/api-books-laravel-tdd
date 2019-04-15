<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateBookTest extends TestCase
{
    use RefreshDatabase;

    private function validCreateData()
    {
        return [
            'title' => 'Some title',
            'author' => 'Some author',
            'language' => 'Some language',
            'original_language' => 'Other language',
            'year_of_publish' => (int) 2000,
        ];
    }
    /**
    *@test
    */
    public function book_can_be_created_with_valid_parameters_if_user_is_logged()
    {
        $lang = factory('App\Language')->create();
        $author = factory('App\Author')->create();

        $response = $this->loggedUser()->json('POST', '/api/books/store', [
            'title' => 'Some title',
            'author' => $author->author,
            'language' => $lang->language,
            'original_language' => $lang->language,
            'year_of_publish' => (int) 2000
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('books',
            [
                'title' => 'Some title',
                'slug' => 'some-title',
                'author_id' => $author->id,
                'language_id' => $lang->id,
                'original_language_id' => $lang->id,
                'year_of_publish' => (int) 2000
            ]
        );
    }

    /**
    *@test
    */
    public function guest_can_not_add_book_even_with_valid_params()
    {
        $lang = factory('App\Language')->create();
        $author = factory('App\Author')->create();

        $response = $this->json('POST', '/api/books/store', [
            'title' => 'Some title',
            'author' => $author->author,
            'language' => $lang->language,
            'original_language' => $lang->language,
            'year_of_publish' => (int) 2000
        ]);

        $response->assertStatus(401);

        $this->assertDatabaseMissing('books',
            [
                'title' => 'Some title',
                'slug' => 'some-title',
                'author_id' => $author->id,
                'language_id' => $lang->id,
                'original_language_id' => $lang->id,
                'year_of_publish' => (int) 2000
            ]
        );
    }


    /**
    *@test
    */
    public function cannot_store_book_if_title_has_not_provided()
    {
        $validData = $this->validCreateData();
        $noTitle = array_splice($validData,1,4);

        $this->loggedUser()->json('POST', '/api/books/store', $noTitle)
        ->assertStatus(422);
        $this->assertDatabaseMissing('books', $noTitle);
    }

    /**
     *@test
     */
    public function cannot_store_book_if_title_is_not_unique()
    {
        factory('App\Book')->create(['title'=>'Some title']);

        $this->loggedUser()->json('POST', '/api/books/store',
            $this->validCreateData())->assertStatus(422);

        $this->assertDatabaseMissing('books',
            $this->validCreateData()
        );
    }

    /**
    *@test
    **/
   public function can_not_store_book_if_year_is_not_number_between_1500_and_2100()
    {
        $changeYear = array_merge($this->validCreateData(),['year_of_publish' => 999]);

        $this->loggedUser()->json('POST', '/api/books/store', $changeYear)
            ->assertStatus(422);
        $this->assertDatabaseMissing('books', $changeYear);
    }
}
