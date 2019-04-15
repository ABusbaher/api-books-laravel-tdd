<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Finding User by column name with provided request
     * @param $column
     * @param $req
     * @return mixed
     */
    public static function foundBy($column,$req)
    {
        return self::where($column,$req)->firstOrFail();
    }

    /**
     * Generate api token (60 random characters) by updating api_token column in users table
     * @return string
     */
    public function generateApiToken()
    {
        return $this->update(['api_token'=>str_random(60)]);
    }

    /**
     * Setting api_token column in users table to null
     * @return null
     */
    public function deleteApiToken()
    {
        return $this->update(['api_token'=>null]);
    }

}
