<?php

namespace Modules\Client\Requests\Order;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class GetAvailableSoltsRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'date' => ['required', 'date_format:Y-m-d', 'after:today'],
                'city_id' => ['required', Rule::exists('cities', 'id')->whereNull('deleted_at')],
                'state_id' => ['required', Rule::exists('states', 'id')->whereNull('deleted_at')],
                'products' => ['required', 'array'],
                'products.*.id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')],
            ];
    }
}
