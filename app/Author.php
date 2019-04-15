<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $fillable = ['id', 'author', 'created_at', 'updated_at'];

    /**
     * Find author by request or create author if not exists and return author id
     * @param $req
     * @return number
     */
    public static function findOrCreateAuthor($req)
    {
        return self::firstOrCreate(['author' => $req])->id;
    }
}
