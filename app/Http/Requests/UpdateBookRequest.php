<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'unique:books|max:255',
            'year_of_publish' => 'integer|min:1500|max:2100',
            'author' => 'required',
            'language' => 'required',
            'original_language' => 'required',
        ];
    }
}
