<?php

namespace App\Http\Requests\User;

use App\Http\Requests\StoreRequestBase;
use Illuminate\Validation\Rules\Password;

class StoreUserRegisterRequest extends StoreRequestBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'username' => 'required|min:4|max:40|unique:users,username',
            'email'    => 'required|email|max:100|unique:users,email',
            'password' => [
                'required' ,
                Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
            ]
        ];
    }
}
