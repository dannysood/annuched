<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // TODO setup proper authorization before golive
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        // TODO make these validation ruels reusable
        return [
            'title' => ['required','string','Min:30','bad_word'],
            'description' => ['required','string','Min:100','bad_word']
        ];
    }
}
