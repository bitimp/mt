<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class LoyaltyPointsTransactionDTO
{
    public function __construct(
        public string $description,
        public ?string $loyaltyPointsRule = null,
        public ?string $paymentId = null,
        public ?float $paymentAmount = null,
        public ?int $paymentTime = null,
        public ?float $pointsAmount = null,
    ) {}

    public static function fromRequest(Request $request): static
    {
        return new static(
            $request->input('description'),
            $request->input('loyalty_points_rule'),
            $request->input('payment_id'),
            $request->input('payment_amount'),
            $request->input('payment_time'),
            $request->input('points_amount'),
        );
    }
}
