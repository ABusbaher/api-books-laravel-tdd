<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateDeleteBookTest extends TestCase
{

    use RefreshDatabase;

    private function validUpdatedData()
    {
        return [
            'title' => 'Some other title',
            'author' => 'Some other author',
            'language' => 'Some language',
            'original_language' => 'Other language',
            'year_of_publish' => (int) 2000,
        ];
    }
    /**
    *@test
    */
    public function logged_user_can_update_book_with_valid_slug_and_valid_parameters()
    {
        $this->withoutExceptionHandling();

        $lang1 = factory('App\Language')->create(['language' => 'Some language']);
        $lang2 = factory('App\Language')->create(['language' => 'Other language']);
        $author = factory('App\Author')->create(['author' => 'Some other author']);

        $book = factory('App\Book')->create(['id'=> 1]);

        $this->loggedUser()->json('PUT', "api/books/update/{$book->slug}",
            $this->validUpdatedData())->assertStatus(200);

        $this->assertDatabaseHas('books',
            [
                'title' => 'Some other title',
                'author_id' => $author->id,
                'language_id' => $lang1->id,
                'original_language_id' => $lang2->id,
                'year_of_publish' => (int) 2000,
            ]
        );
    }

    /**
    *@test
    */
    public function guest_can_not_update_book_even_with_valid_slug_and_parameters()
    {
        $book = factory('App\Book')->create(['id'=> 1]);

        $this->json('PUT', "api/books/update/{$book->slug}",$this->validUpdatedData())
            ->assertStatus(401);
        $this->assertDatabaseHas('books',
            [
                'title' => $book->title,
                'author_id' => $book->author_id,
                'language_id' => $book->language_id,
                'original_language_id' => $book->original_language_id,
                'year_of_publish' => $book->year_of_publish,
            ]
        );
    }

    /**
    *@test
    */
    public function book_can_be_updated_even_title_or_year_of_publish_is_not_provided()
    {
        $lang1 = factory('App\Language')->create(['language' => 'Some language']);
        $lang2 = factory('App\Language')->create(['language' => 'Other language']);
        $author = factory('App\Author')->create(['author' => 'Some other author']);


        $book = factory('App\Book')->create(['id'=> 1]);
        $validData = $this->validUpdatedData();
        $noTitle = array_splice($validData,1,4);

        $this->loggedUser()->json('PUT', "api/books/update/{$book->slug}",
            $noTitle)->assertStatus(200);
        $this->assertDatabaseHas('books',
            [
                'title' => $book->title,
                'author_id' => $author->id,
                'language_id' => $lang1->id,
                'original_language_id' => $lang2->id,
                'year_of_publish' => (int) 2000,
            ]
        );
    }

    /**
    *@test
    */
    public function cannot_update_book_if_book_slug_does_not_exists_even_if_data_is_valid()
    {
        factory('App\Book')->create(['title'=> 'title']);
        $this->loggedUser()->json('PUT', "api/books/update/invalid-slug",
            $this->validUpdatedData())->assertStatus(404);
    }

    /**
    *@test
    */
    public function book_can_not_be_updated_if_language_or_original_language_or_author_are_not_provided()
    {
        $book = factory('App\Book')->create();
        $validData = $this->validUpdatedData();

        $noAuthor =array_splice($validData,2);
        $this->loggedUser()->json('PUT', "api/books/update/$book->slug",$noAuthor)->assertStatus(422);
    }

    /**
    *@test
    */
    public function book_can_be_deleted_if_valid_slug_is_provided_and_user_is_logged()
    {
        $book = factory('App\Book')->create();
        $this->loggedUser()->json('DELETE', "api/books/destroy/$book->slug")->assertStatus(204);
        $this->assertDatabaseMissing('books',['id' => 1]);
    }

    /**
     *@test
     */
    public function guest_can_not_delete_a_book()
    {
        $book = factory('App\Book')->create();
        $this->json('DELETE', "api/books/destroy/$book->slug")->assertStatus(401);
        $this->assertDatabaseHas('books',['id' => 1]);
    }

    /**
    *@test
    */
    public function if_invalid_slug_is_provided_for_deleting_book_it_throws_404_status()
    {
        factory('App\Book')->create(['title'=> 'title']);
        $this->loggedUser()->json('DELETE', "api/books/destroy/invalid-slug")->assertStatus(404);
        $this->assertDatabaseHas('books',['id' => 1]);
    }
}
