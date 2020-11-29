<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PinPurchaseRequest extends FormRequest
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
            'pins'=>'required|numeric',
            'pop'=>'bail|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'payment_mode'=>'bail|required|string'
        ];
    }
}
