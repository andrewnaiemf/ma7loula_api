<?php

namespace Modules\Core\Requests\Address;

use Modules\Core\Requests\PublicRequest;

class ListCitiesRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'state_id' => ['required', 'exists:states,id'],
        ];
    }
}