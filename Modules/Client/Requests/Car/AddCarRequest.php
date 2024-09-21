<?php

namespace Modules\Client\Requests\Car;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class AddCarRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'car_id' => [
                    'required', Rule::exists('cars', 'id')->whereNull('deleted_at')
                ],
                'is_default' => [
                    'required', 'boolean'
                ]
            ];
    }
}
