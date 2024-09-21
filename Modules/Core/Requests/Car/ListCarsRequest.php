<?php

namespace Modules\Core\Requests\Car;

use Modules\Core\Requests\PublicRequest;

class ListCarsRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'car_brand_id' => ['required', 'exists:car_brands,id'],
            'car_model_id' => ['required', 'exists:car_models,id'],
        ];
    }
}