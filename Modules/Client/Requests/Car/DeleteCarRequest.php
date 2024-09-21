<?php

namespace Modules\Client\Requests\Car;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class DeleteCarRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'user_car_id' => [
                    'required', Rule::exists('user_car', 'id')->whereNull('deleted_at')
                ]
            ];
    }
}
