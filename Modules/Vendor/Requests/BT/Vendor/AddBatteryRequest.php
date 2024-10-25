<?php

namespace Modules\Vendor\Requests\BT\Vendor;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\Request;

class AddBatteryRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->vendor;
    }

    public function rules(): array
    {
        $rules =  [
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'images' => ['required', 'array'],
            'images.*' => ['required', 'string'],
            'default_image' => ['required', 'string'],
            'brand_id' => ['required',  Rule::exists('product_brands', 'id')->whereNull('deleted_at')->where('product_category_id', Product::BatteriesCategory)],
            'stock' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'price_before_discount' => ['nullable', 'numeric'],
            'sku' => ['required', 'string'],
            'car_ids' => ['required', 'array'],
            'car_ids.*' => ['required', Rule::exists('cars', 'id')->whereNull('deleted_at')],
            'year_of_manufacture' => ['required', 'date_format:Y'],
            'voltage' => ['required', 'string'],
        ];

        return $rules;
    }
}
