<?php

namespace App\Http\Requests\User;

use App\Http\Requests\StoreRequestBase;

class StoreLoginRequest extends StoreRequestBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }
}
