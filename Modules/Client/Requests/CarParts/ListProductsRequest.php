<?php

namespace Modules\Client\Requests\CarParts;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ListProductsRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'category_id' => [
                    'required_without:name',
                    Rule::exists('product_categories', 'id')->whereNull('deleted_at')
                ],
                'name' => [
                    'string',
                    'required_without:category_id'
                ],
                'car_id' => [
                    'required',
                    Rule::exists('cars', 'id')->whereNull('deleted_at')
                ]
            ];
    }
}
