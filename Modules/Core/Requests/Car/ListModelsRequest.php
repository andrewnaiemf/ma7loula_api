<?php

namespace Modules\Core\Requests\Car;

use Modules\Core\Requests\PublicRequest;

class ListModelsRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'car_brand_id' => ['required', 'exists:car_brands,id'],
        ];
    }
}