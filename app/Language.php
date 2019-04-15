<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{

    protected $fillable = ['id','language', 'created_at', 'updated_at'];

    public function books()
    {
        return $this->hasMany('App\Books', 'language_id');
    }

    public function oBooks()
    {
        return $this->hasMany('App\Books', 'original_language_id');
    }

    /**
     * Find language by request or create language if not exists and return language id
     * @param $req
     * @return mixed
     */
    public static function findOrCreateLanguage($req)
    {
        return self::firstOrCreate(['language'=>$req])->id;
    }
}
