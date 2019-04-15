<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = [];

    public function language()
    {
        return $this->belongsTo('App\Language', 'language_id');
    }

    public function original_language()
    {
        return $this->belongsTo('App\Language', 'original_language_id');
    }

    public function author()
    {
        return $this->belongsTo('App\Author', 'author_id');
    }

    /**
     * Search Books by author name or by year of publish
     * 10 results, newest record first
     * @param $req
     * @return mixed
     */
    public static function searchBooksByAuthorOrPublishYear($req)
    {
        return self::whereHas
        ('author', function($q) use ($req) {
            $q->where('author', 'LIKE', '%' . $req . '%');
        })
            ->orWhere('year_of_publish', 'LIKE', '%' . $req . '%')
            ->limit(10)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    /**
     * Finding book by column name with provided request
     * @param $column
     * @param $req
     * @return mixed
     */
    public static function foundBy($column,$req)
    {
        return self::where($column,$req)->firstOrFail();
    }

    /**
     * If request not provided return value of column, otherwise return request data
     * @param $column
     * @param string $req
     * @return string
     */
    public function requestExistsOrEmpty($column, $req = '')
    {
        return empty($req) ? $column : $req;
    }

    /**
     * Associate Language table with language_id from Books table
     * @param $req
     * @return Model
     */
    public function associateLanguage($req)
    {
        return $this->language()->associate(Language::firstOrCreate(['language' => $req]));
    }

    /**
     * Associate Language table with original_language_id from Books table
     * @param $req
     * @return Model
     */
    public function associateOriginalLanguage($req)
    {
        return $this->original_language()->associate(Language::firstOrCreate(['language' => $req]));
    }

    /**
     * Associate Author table with author_id from Books table
     * @param $req
     * @return Model
     */
    public function associateAuthor($req)
    {
        return $this->author()->associate(Author::firstOrCreate(['author' => $req]));
    }
}
