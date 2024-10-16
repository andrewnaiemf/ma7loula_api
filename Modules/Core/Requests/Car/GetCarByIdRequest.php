<?php

namespace Modules\Core\Requests\Car;

use Modules\Core\Requests\PublicRequest;

class GetCarByIdRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'car_id' => ['required', 'exists:cars,id'],
        ];
    }
}