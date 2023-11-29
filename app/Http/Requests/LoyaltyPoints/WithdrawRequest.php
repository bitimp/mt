<?php

namespace App\Http\Requests\LoyaltyPoints;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'account_type' => [
                'required',
                'in:phone,card,email',
            ],
            'account_id' => [
                'required',
                'string',
            ],
            'description' => [
                'required',
                'string',
            ],
            'points_amount' => [
                'required',
                'numeric',
            ],
        ];
    }
}
