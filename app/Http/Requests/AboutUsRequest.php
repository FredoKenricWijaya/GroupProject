<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AboutUsRequest extends FormRequest
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];

        if ($this->isMethod('post')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } elseif ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['image'] = 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048';
            $rules['description'] = 'sometimes|required|string';
        }

        return $rules;
    }
}
