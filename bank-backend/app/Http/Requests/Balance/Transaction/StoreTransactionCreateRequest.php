<?php

namespace App\Http\Requests\Balance\Transaction;

use App\Http\Requests\StoreRequestBase;

class StoreTransactionCreateRequest extends StoreRequestBase
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            "amount" => "required|numeric|min:0|max:999999999",
            "description" => "required",
            "transaction_type" => "required|in:C,D",
        ];
    }
}
