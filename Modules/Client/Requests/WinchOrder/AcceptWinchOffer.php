<?php

namespace Modules\Client\Requests\WinchOrder;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class AcceptWinchOffer extends PublicRequest
{
    public function rules(): array
    {
        return [
            'order_id' => ['required', Rule::exists('orders', 'id')->whereNull('deleted_at')->where('user_id', Auth::user()->id)],
            'worker_id' => ['required', Rule::exists('workers', 'id')->whereNull('deleted_at')],
            'vendor_id' => ['required', Rule::exists('vendors', 'id')->whereNull('deleted_at')],
        ];
    }
}
