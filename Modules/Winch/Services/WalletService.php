<?php

namespace Modules\Winch\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\WithdrawRequest;
use Illuminate\Support\Facades\Auth;
use Modules\Winch\Requests\Transactions\CreateWithdrawRequest;
use Modules\Winch\Resources\WithdrawRequestResource;

class WalletService
{
    private User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function listTransactions()
    {
        return Transaction::where('user_id', $this->user->id)->orderBy('id', 'desc');
    }

    public function getBalance()
    {
        $user = Auth::user();
        return $user->worker ? (float)  $user->worker->balance : 0;
    }

    public function listWithdrawRequests()
    {
        return WithdrawRequest::where('user_id', $this->user->id)->orderBy('id', 'desc');
    }

    public function createWithdrawRequests(CreateWithdrawRequest $request)
    {
        $request = WithdrawRequest::create([
            'user_id' => $this->user->id,
            'amount' => $request->input('amount'),
            'method' => $request->input('method'),
            'status' => 'new'
        ]);

        return new WithdrawRequestResource($request);
    }

    public static function listWithdrawMethods(){
        return ['vodafone cash', 'instapay', 'bank transfer', 'etislate cash', 'cash'];
    }
}
