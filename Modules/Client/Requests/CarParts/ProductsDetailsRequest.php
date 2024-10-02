<?php

namespace Modules\Client\Requests\CarParts;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ProductsDetailsRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'id' => [
                    'integer',
                    'required',
                    Rule::exists('products', 'id')->where('status', 'published')
                ],
            ];
    }
}
