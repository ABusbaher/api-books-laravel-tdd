<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Language;
use App\Http\Resources\Book as BookResource;
use App\Book as Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    /**
     * User need to be logged in for creating, updating and deleting books
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['only' => ['store','update','destroy']]);
    }

    /**
     * Finding Book by slug instead of ID
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * api/books?page=1
     * Getting all books (5 per page) or
     * searching them via author name or year of publish if search parameter is provided
     * api/books?search=authorNameOrYearOfPublish
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        if(!empty($request['search'])) {
            return BookResource::collection(
                Book::searchBooksByAuthorOrPublishYear($request['search']));
        }
        return BookResource::collection(Book::paginate(5));
    }

    /**
     * Finding specific book by slug name
     * api/books/book-slug-name
     * @param $slug
     * @return BookResource
     */
    public function show($slug)
    {
        try{
            return new BookResource(Book::foundBy('slug',$slug));
        }catch (ModelNotFoundException $e){
            return response()->json(['Book does not exists'],404);
        }


    }

    /**
     * Creating new Book - only logged users
     * api/books/store
     * @param StoreBookRequest $request (title,year of publish, language, original language, author)
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreBookRequest $request)
    {
        $book = Book::create([
            'title'                => $request->title,
            'slug'                 => str_slug($request->title),
            'year_of_publish'      => (int) $request->year_of_publish,
            'language_id'          => Language::findOrCreateLanguage($request->language),
            'original_language_id' => Language::findOrCreateLanguage($request->original_language),
            'author_id'            => Author::findOrCreateAuthor($request->author)
        ]);
        return response()->json($book,201);
    }

    /**
     * Updating existing book - only logged users
     * api/books/update/slug-name
     * @param UpdateBookRequest $request (title,year of publish, language, original language, author)
     * @param $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateBookRequest $request, $slug)
    {
        try{
            $book = Book::foundBy('slug',$slug);
            /** @var Book $book */
            $book->title                         = $book->requestExistsOrEmpty($book->title, $request->title);//if request not provided leave old value
            $book->slug                          = $book->requestExistsOrEmpty(str_slug($book->title), str_slug($request->title));
            $book->year_of_publish               = $book->requestExistsOrEmpty($book->year_of_publish, (int) $request->year_of_publish);
            $book->associateLanguage($request->language);
            $book->associateAuthor($request->author);
            $book->associateOriginalLanguage($request->original_language);
            $book->update();
            return response()->json(['Book successfully updated'],200);
        }catch (ModelNotFoundException $e){
            return response()->json(['Book does not exists'],404);
        }

    }

    /**
     * Deleting book by slug name - only logged users
     * api/books/destroy/slug-name
     * @param $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($slug)
    {
        try{
            Book::foundBy('slug',$slug)->delete();
            return response()->json([],204);
        }catch (ModelNotFoundException $e){
            return response()->json(['Book does not exists'],404);
        }
    }

}
