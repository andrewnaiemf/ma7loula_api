<?php

namespace Modules\Client\Requests\Tire;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ListBrandsRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'car_id' => [
                    'required',
                    Rule::exists('cars', 'id')->whereNull('deleted_at')
                ]
            ];
    }
}
