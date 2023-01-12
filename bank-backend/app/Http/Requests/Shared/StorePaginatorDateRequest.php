<?php

namespace App\Http\Requests\Shared;

use App\Http\Requests\StoreRequestBase;

class StorePaginatorDateRequest extends StoreRequestBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "date" => "required|date|date_format:Y-m-d",
            "status" => "nullable|in:A,P,R"
        ];
    }
}
