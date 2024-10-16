<?php

namespace Modules\Client\Requests\CarParts;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ListProductsByIdRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'product_ids' => [
                    'required',
                    'array'
                ],
                'product_ids.*' => [
                    'required',
                    Rule::exists('products', 'id')->whereNull('deleted_at')
                ],
            ];
    }
}
