<?php

namespace App\Http\Requests\LoyaltyPoints;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
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
            'loyalty_points_rule' => [
                'required',
                'string',
            ],
            'description' => [
                'required',
                'string',
            ],
            'payment_id' => [
                'nullable',
                'string',
            ],
            'payment_amount' => [
                'required',
                'numeric',
            ],
            'payment_time' => [
                'nullable',
                'integer',
            ],
        ];
    }
}
