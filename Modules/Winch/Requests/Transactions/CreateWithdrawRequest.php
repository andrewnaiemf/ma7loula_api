<?php

namespace Modules\Winch\Requests\Transactions;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;
use Modules\Winch\Services\WalletService;

class CreateWithdrawRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->worker;
    }

    public function rules(): array
    {
        $rules =  [
            'amount' => ['required', 'numeric', 'min:1', 'max:' . Auth::user()->worker->balance],
            'method' => ['required', 'in:' . implode(',', WalletService::listWithdrawMethods())]
        ];

        return $rules;
    }

    public function messages(): array
    {
        $rules =  [
            'amount' => [
                'max' => 'رصيد غير كافي'
            ]
        ];

        return $rules;
    }
}
