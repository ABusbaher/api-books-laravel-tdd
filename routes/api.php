<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/books', 'BooksController@index')->name('allBooks');
Route::get('/books/{slug}', 'BooksController@show')->name('singleBook');
Route::post('/books/store', 'BooksController@store')->name('storeBook');
Route::put('/books/update/{slug}', 'BooksController@update')->name('editBook');
Route::delete('/books/destroy/{slug}', 'BooksController@destroy')->name('deleteBook');

Route::post('/auth/register', 'Api\AuthController@register')->name('registerUser');
Route::post('/auth/login', 'Api\AuthController@login')->name('loginUser');
Route::post('/auth/logout', 'Api\AuthController@logout')->name('logoutUser');