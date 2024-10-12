<?php

namespace Modules\Client\Requests\Order;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class RateOrderRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'order_id' => ['required', Rule::exists('orders', 'id')->whereNull('deleted_at')],
                'products' => ['nullable', 'numeric', 'min:0', 'max:5'],
                'services' => ['nullable', 'numeric', 'min:0', 'max:5'],
                'worker' => ['nullable', 'numeric', 'min:0', 'max:5'],
                'comment' => ['nullable', 'string', 'max:150'],
            ];
    }
}
