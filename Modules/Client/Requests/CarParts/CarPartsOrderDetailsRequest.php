<?php

namespace Modules\Client\Requests\CarParts;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class CarPartsOrderDetailsRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'id' => [
                    'required',
                    Rule::exists('orders', 'id')->whereNull('deleted_at')->where('user_id', Auth::user()->id)->where('type', 'car-parts')
                ],
            ];
    }
}
