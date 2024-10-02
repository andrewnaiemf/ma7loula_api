<?php

namespace Modules\Client\Requests\Battery;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ListBatteriesRequest extends PublicRequest
{
    public function rules(): array
    {
        return
            [
                'car_id' => [
                    'required',
                    Rule::exists('cars', 'id')->whereNull('deleted_at')
                ],
                'brand_id' => [
                    'required',
                    Rule::exists('product_brands', 'id')->whereNull('deleted_at')->where('product_category_id', Product::BatteriesCategory)
                ],
                'voltage' => [
                    'required'
                ]
            ];
    }
}
