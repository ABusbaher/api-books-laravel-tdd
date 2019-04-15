<?php

namespace Tests\Unit;

use App\Book;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    /**
    *@test
    */
    public function books_can_be_searched_by_author_or_by_year_of_publish()
    {
        $author1 = factory('App\Author')->create(['author' =>'Ivo Andrić']);
        $author2 = factory('App\Author')->create(['author' =>'Miša Selimović']);

        factory('App\Book')->create(['author_id' => $author1->id,'year_of_publish' => 1958]);
        factory('App\Book')->create(['author_id' => $author2->id,'year_of_publish' => 1958]);
        factory('App\Book')->create(['author_id' => $author1->id,'year_of_publish' => 1958]);

        $result = Book::searchBooksByAuthorOrPublishYear( 'Ivo');
        $this->assertEquals(count($result), 2);

        $result2 = Book::searchBooksByAuthorOrPublishYear( 'Miša');
        $this->assertEquals(count($result2), 1);

        $result3 = Book::searchBooksByAuthorOrPublishYear( 1958);
        $this->assertEquals(count($result3), 3);
    }

    /**
     *@test
     */
    public function book_can_be_found_by_slug()
    {
        $book1 = factory('App\Book')->create(['slug' => 'some-slug']);
        $book2 = factory('App\Book')->create(['slug' => 'other-slug']);

        $this->assertEquals($book1::foundBy('slug',$book1->slug),$book1->fresh());
        $this->assertNotEquals($book1::foundBy('slug',$book1->slug),$book2->fresh());
    }

    /**
    *@test
    */
    public function it_return_column_value_if_request_is_empty_otherwise_return_request()
    {
        $book = factory('App\Book')->create(['title' => 'some title']);
        $res = $book->requestExistsOrEmpty($book->title, 'New title');
        $this->assertEquals($res, 'New title');

        $res2 = $book->requestExistsOrEmpty($book->title);
        $this->assertEquals($res2, $book->title);
    }

    /**
    *@test
    */
    public function book_can_by_associated_by_language_original_language_and_author()
    {
        $lang = factory('App\Language')->create();
        $lang2 = factory('App\Language')->create();
        $author = factory('App\Author')->create();
        $book = factory('App\Book')->create();

        $book->associateLanguage($lang->language);
        $this->assertEquals($book->language_id,$lang->id);

        $book->associateOriginalLanguage($lang2->language);
        $this->assertEquals($book->original_language_id,$lang2->id);

        $book->associateAuthor($author->author);
        $this->assertEquals($book->author_id,$author->id);
    }
}
