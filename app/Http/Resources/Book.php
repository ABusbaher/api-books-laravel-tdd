<?php

namespace App\Http\Resources;

use App\Author;
use App\Language;

use Illuminate\Http\Resources\Json\Resource;

class Book extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        return [
            //'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'year_of_publish' => $this->year_of_publish,
            'language' => $this->language->language,
            'original_language' => $this->original_language->language,
            'author' => $this->author->author,
            'link' => route('singleBook',$this->slug),
            'all books' => route('allBooks')
        ];
    }
}
