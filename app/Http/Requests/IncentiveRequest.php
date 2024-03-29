<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncentiveRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'bail|required|string',
            'level' => 'bail|required|numeric|exists:levels,id',
            'description'=>'bail|required|string'
        ];
    }
}
