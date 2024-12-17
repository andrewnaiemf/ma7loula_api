<?php

namespace Modules\Winch\Controllers;

use Modules\Core\Controllers\Controller;
use Modules\Winch\Requests\Transactions\CreateWithdrawRequest;
use Modules\Winch\Resources\TransactionResource;
use Modules\Winch\Resources\WithdrawRequestResource;
use Modules\Winch\Services\WalletService;

class WalletController extends Controller
{
    public function __construct(private WalletService $walletService) {}

    public function listTransactions()
    {
        return $this->listResponse(
            'transactions',
            $this->walletService->listTransactions(),
            new TransactionResource([]),
            null,
            [
                'balance' => $this->walletService->getBalance()
            ]
        );
    }

    public function listWithdrawRequests()
    {
        return $this->listResponse(
            'requests',
            $this->walletService->listWithdrawRequests(),
            new WithdrawRequestResource([])
        );
    }

    public function createWithdrawRequests(CreateWithdrawRequest $request)
    {
        return $this->successResponse([
            'request' =>  $this->walletService->createWithdrawRequests($request)
        ]);
    }

    public function listWithdrawMethods()
    {
        return $this->successResponse([
            'methods' =>  $this->walletService->listWithdrawMethods()
        ]);
    }
}
