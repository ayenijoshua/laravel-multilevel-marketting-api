<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntryPaymentStoreRequest extends FormRequest
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
            'user_id'=>'bail|required|exists:users,id',
            'pop' => 'image|mimes:jpeg,png,jpg,gif,svg|max:1048',
            'referrer'=>'bail|nullable|exists:users,username'
        ];
    }
}
