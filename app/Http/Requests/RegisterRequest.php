<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name'=>'bail|required|string',
            'username'=>'bail|required|string|unique:users',
            'email'=>'bail|required|email',
            'password'=>'bail|required|string|confirmed',
            //'referral id'=>'bail|required|string|exists:users,username',
            'terms'=>'bail|required|accepted'
        ];
    }
}
