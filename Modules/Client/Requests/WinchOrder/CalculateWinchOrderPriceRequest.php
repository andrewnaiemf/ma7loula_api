<?php

namespace Modules\Client\Requests\WinchOrder;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class CalculateWinchOrderPriceRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'user_car_id' => ['required', Rule::exists('user_car', 'id')->whereNull('deleted_at')],
                'from_lat' => ['required', 'decimal:3,999'],
                'from_lon' => ['required', 'decimal:3,999'],
                'to_lat' => ['required', 'decimal:3,999'],
                'to_lon' => ['required', 'decimal:3,999'],
                'distance_in_meters' => ['required', 'decimal:0,2'],
                'duration_in_minutes' => ['required', 'decimal:0,2'],
            ];
    }
}
