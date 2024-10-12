<?php

namespace Modules\Client\Requests\Order;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class UpdateOrderStatusRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'id' => [
                    'required', Rule::exists('orders', 'id')->whereNull('deleted_at')
                ],
                'status' => [
                    'required', 'string'
                ]
            ];
    }
}
