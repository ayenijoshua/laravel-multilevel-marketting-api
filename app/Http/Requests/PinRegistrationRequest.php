<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinRegistrationRequest extends FormRequest
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
            'seller_id'=>'bail|required|numeric|exists:users,id',
            'buyer_id'=>'bail|required|numeric|exists:users,id',
            'referrer'=>'bail|exists:users,username'
        ];
    }
}
