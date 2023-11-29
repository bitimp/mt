<?php

namespace App\Http\Controllers;

use App\Contracts\Services\LoyaltyPointsManager;
use App\DTOs\LoyaltyPointsTransactionDTO;
use App\Http\Requests\LoyaltyPoints\CancelRequest;
use App\Http\Requests\LoyaltyPoints\DepositRequest;
use App\Http\Requests\LoyaltyPoints\WithdrawRequest;
use App\Models\LoyaltyAccount;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Log\Logger;

class LoyaltyPointsController extends Controller
{
    protected Logger $logger;
    protected LoyaltyPointsManager $loyaltyPointsManager;

    public function __construct(Logger $logger, LoyaltyPointsManager $loyaltyPointsManager)
    {
        $this->logger = $logger;
        $this->loyaltyPointsManager = $loyaltyPointsManager;
    }

    public function deposit(DepositRequest $request)
    {
        $this->logger->info('Deposit transaction input: ' . print_r($request->safe()->all(), true));

        // @todo: Не придумал красивое решение, если нужно как раньше
        // if (($type == 'phone' || $type == 'card' || $type == 'email') && $id != '') {
        // } else {
        //     Log::info('Wrong account parameters');
        //     throw new \InvalidArgumentException('Wrong account parameters');
        // }

        try {
            return $this->loyaltyPointsManager->deposit(
                $this->fetchLoyaltyAccount($request->input('account_type'), $request->input('account_id')),
                LoyaltyPointsTransactionDTO::fromRequest($request)
            );
        } catch (ModelNotFoundException $e) {
            $this->logger->info('Account is not found');
            return response()->json(['message' => 'Account is not found'], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function cancel(CancelRequest $request)
    {
        // @todo: Не придумал красивое решение, если нужно как раньше
        // if ($reason == '') {
        //     return response()->json(['message' => 'Cancellation reason is not specified'], 400);
        // }

        try {
            return $this->loyaltyPointsManager->cancelTransaction(
                $request->input('id'),
                $request->input('cancellation_reason')
            );
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    public function withdraw(WithdrawRequest $request)
    {
        $this->logger->info('Withdraw loyalty points transaction input: ' . print_r($request->safe()->all(), true));

        // @todo: Не придумал красивое решение, если нужно как раньше
        // if (($type == 'phone' || $type == 'card' || $type == 'email') && $id != '') {
        // } else {
        //     Log::info('Wrong account parameters');
        //     throw new \InvalidArgumentException('Wrong account parameters');
        // }

        try {
            return $this->loyaltyPointsManager->withdraw(
                $this->fetchLoyaltyAccount($request->input('account_type'), $request->input('account_id')),
                LoyaltyPointsTransactionDTO::fromRequest($request)
            );
        } catch (ModelNotFoundException $e) {
            $this->logger->info('Account is not found');
            return response()->json(['message' => 'Account is not found'], Response::HTTP_BAD_REQUEST);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    // @todo: Можно сделать лучше. Например, через репозиторий
    /**
     * @param string $field
     * @param string $value
     *
     * @return LoyaltyAccount
     *
     * @throws ModelNotFoundException
     */
    protected function fetchLoyaltyAccount(string $field, string $value): LoyaltyAccount
    {
        /** @var LoyaltyAccount|null $loyaltyAccount */
        $loyaltyAccount = LoyaltyAccount::query()
            ->where($field, $value)
            ->firstOrFail()
        ;

        return $loyaltyAccount;
    }
}
