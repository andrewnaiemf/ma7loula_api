<?php

namespace Modules\Client\Requests\Order;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class CreateOrderRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'address_id' => [
                    'required',
                    Rule::exists('addresses', 'id')->whereNull('deleted_at')->where('user_id', Auth::user()->id)
                ],
                'user_car_id' => [
                    'required',
                    Rule::exists('user_car', 'id')->whereNull('deleted_at')->where('user_id', Auth::user()->id)
                ],
                'payment_method' => [
                    'required',
                    'in:visa,cash'
                ],
                'delivery_type' => [
                    'required',
                    'in:fast,scheduled'
                ],
                'delivery_time' => [
                    'required_if:delivery_type,scheduled',
                    'after:today',
                    'date_format:Y-m-d H:i'
                ],
                'products' => [
                    'required',
                    'array'
                ],
                'products.*.id' => [
                    'required',
                    Rule::exists('products', 'id')->whereNull('deleted_at')
                ],
                'products.*.qty' => [
                    'required',
                    'numeric',
                    'min:1',
                ],
                'has_service' => [
                    'boolean',
                    'nullable'
                ]
            ];
    }
}
