<?php

namespace Modules\Client\Requests\Tire;

use App\Models\Product;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ListTiresRequest extends PublicRequest
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
                    Rule::exists('product_brands', 'id')->where('product_category_id', Product::TiresCategory)->whereNull('deleted_at')
                ],
                'type' => ['required', 'in:normal,flat'],
                'height' => ['required', 'integer'],
                'width' => ['required', 'integer'],
                'length' => ['required'],
            ];
    }
}
