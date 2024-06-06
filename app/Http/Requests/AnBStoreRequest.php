<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnBStoreRequest extends FormRequest
{
    public function authorize()
    {
        // Set to true if you want to allow all users to make this request.
        // Update with actual authorization logic if needed.
        return true;
    }

    public function rules()
    {
        $rules = [
            'description' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:102400',
        ];

        return $rules;
    }
}
