<?php

namespace App\Contracts\Services;

use App\DTOs\LoyaltyPointsTransactionDTO;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyPointsTransaction;
use Illuminate\Log\Logger;

interface LoyaltyPointsManager
{
    public function __construct(Logger $logger);
    public function deposit(LoyaltyAccount $account, LoyaltyPointsTransactionDTO $transactionDTO): LoyaltyPointsTransaction;
    public function withdraw(LoyaltyAccount $account, LoyaltyPointsTransactionDTO $transactionDTO): LoyaltyPointsTransaction;
    public function cancelTransaction(int $id, string $cancellationReason): LoyaltyPointsTransaction;
}
