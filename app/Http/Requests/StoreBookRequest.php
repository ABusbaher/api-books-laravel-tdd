<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title' => 'required|unique:books|max:255',
            'author' => 'required',
            'language' => 'required',
            'original_language' => 'required',
            'year_of_publish' => 'required|integer|min:1500|max:2100'
        ];
    }
}
