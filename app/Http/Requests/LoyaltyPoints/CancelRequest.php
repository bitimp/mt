<?php

namespace App\Http\Requests\LoyaltyPoints;

use Illuminate\Foundation\Http\FormRequest;

class CancelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [
            'transaction_id' => [
                'required',
                'integer',
            ],
            'cancellation_reason' => [
                'required',
                'string',
            ]
        ];
    }
}
