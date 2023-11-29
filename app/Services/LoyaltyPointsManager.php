<?php

namespace App\Services;

use App\Contracts\Services\LoyaltyPointsManager as LoyaltyPointsManagerContract;
use App\DTOs\LoyaltyPointsTransactionDTO;
use App\Mail\LoyaltyPointsReceived;
use App\Models\LoyaltyAccount;
use App\Models\LoyaltyPointsTransaction;
use Exception;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Mail;

// @todo: Не самое лучшее название сервиса
class LoyaltyPointsManager implements LoyaltyPointsManagerContract
{
    protected Logger $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param LoyaltyAccount $account
     * @param LoyaltyPointsTransactionDTO $transactionDTO
     *
     * @return LoyaltyPointsTransaction
     *
     * @throws Exception
     */
    public function deposit(LoyaltyAccount $account, LoyaltyPointsTransactionDTO $transactionDTO): LoyaltyPointsTransaction
    {
        if (!$account->active) {
            $this->logger->info('Account is not active');
            // @todo: Сделать нормальное исключение
            throw new Exception('Account is not active');
        }

        /** @var LoyaltyPointsTransaction $transaction */
        $transaction = LoyaltyPointsTransaction::performPaymentLoyaltyPoints(
            $account->id,
            $transactionDTO->loyaltyPointsRule,
            $transactionDTO->description,
            $transactionDTO->paymentId,
            $transactionDTO->paymentAmount,
            $transactionDTO->paymentTime,
        );

        $this->logger->info($transaction);

        // @todo: Переделать отправку нотификаций
        if ($account->isAllowedEmailNotifications) {
            Mail::to($account)
                ->send(new LoyaltyPointsReceived(
                    $transaction->points_amount,
                    $account->getBalance()
                ));
        }

        // @todo: Переделать отправку нотификаций
        if ($account->isAllowedPhoneNotifications) {
            // instead SMS component
            $this->logger->info("You received {$transaction->points_amount}. Your balance {$account->getBalance()}");
        }

        return $transaction;
    }

    /**
     * @param LoyaltyAccount $account
     * @param LoyaltyPointsTransactionDTO $transactionDTO
     *
     * @return LoyaltyPointsTransaction
     *
     * @throws Exception
     */
    public function withdraw(LoyaltyAccount $account, LoyaltyPointsTransactionDTO $transactionDTO): LoyaltyPointsTransaction
    {
        if (!$account->active) {
            $this->logger->info('Account is not active');
            // @todo: Сделать нормальное исключение
            throw new Exception('Account is not active');
        }

        if ($transactionDTO->pointsAmount <= 0) {
            $this->logger->info('Wrong loyalty points amount');
            // @todo: Сделать нормальное исключение
            throw new Exception('Wrong loyalty points amount');
        }

        // @todo: Должна ли быть общая транзакция и блокировка? Может возникнуть ситуация, когда произойдет параллельное списание
        if ($account->getBalance() <= $transactionDTO->pointsAmount) {
            $this->logger->info('Insufficient funds');
            // @todo: Сделать нормальное исключение
            throw new Exception('Insufficient funds');
        }

        /** @var LoyaltyPointsTransaction $transaction */
        $transaction = LoyaltyPointsTransaction::withdrawLoyaltyPoints(
            $account->id,
            $transactionDTO->pointsAmount,
            $transactionDTO->description
        );

        $this->logger->info($transaction);

        // @todo: Должны ли быть уведомления, которые есть при начислении?

        return $transaction;
    }

    /**
     * @param int $id
     * @param string $cancellationReason
     *
     * @return LoyaltyPointsTransaction
     *
     * @throws Exception
     */
    public function cancelTransaction(int $id, string $cancellationReason): LoyaltyPointsTransaction
    {
        /** @var LoyaltyPointsTransaction|null $transaction */
        $transaction = LoyaltyPointsTransaction::query()
            ->where('id', $id)
            ->where('canceled', 0)
            ->first()
        ;

        if (!$transaction) {
            // @todo: Сделать нормальное исключение
            throw new Exception('Transaction is not found');
        }

        $transaction->cancel($cancellationReason);

        // @todo: Должны ли быть уведомления, которые есть при начислении?
        // @todo: Должно ли быть логгирование?

        return $transaction;
    }
}
