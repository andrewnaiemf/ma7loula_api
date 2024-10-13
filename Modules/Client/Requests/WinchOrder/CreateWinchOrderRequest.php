<?php

namespace Modules\Client\Requests\WinchOrder;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class CreateWinchOrderRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'user_car_id' => ['required', Rule::exists('user_car', 'id')->whereNull('deleted_at')],
                'from_lat' => ['required', 'decimal:3,999'],
                'from_lon' => ['required', 'decimal:3,999'],
                'from_text' => ['required', 'string'],
                'to_lat' => ['required', 'decimal:3,999'],
                'to_lon' => ['required', 'decimal:3,999'],
                'to_text' => ['required', 'string'],
                'distance_in_meters' => ['required', 'decimal:0,2'],
                'duration_in_minutes' => ['required', 'decimal:0,2'],
                'price' => ['required', 'decimal:0,2'],
                'user_car_id' => [
                    'required',
                    Rule::exists('user_car', 'id')->whereNull('deleted_at')->where('user_id', Auth::user()->id)
                ],
                'payment_method' => [
                    'required',
                    'in:visa,cash'
                ],
            ];
    }
}
