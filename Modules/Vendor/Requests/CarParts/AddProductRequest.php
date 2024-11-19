<?php

namespace Modules\Vendor\Requests\CarParts;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Requests\Request;

class AddProductRequest extends Request
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
            'category_id' => ['required', Rule::exists('product_categories', 'id')->whereNull('deleted_at')->whereNotIn('id', [1,2])],
        ];

        return $rules;
    }

    protected function passedValidation(): void
    {
        $errors = [];
        if ($this->input('images')) {
            foreach ($this->input('images') as $image) {
                $filename = $image;
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $path = storage_path('app/public/temp/' . $filename);

                if (!file_exists($path)) {
                    $errors['images'][] = 'file required';
                }

                if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                    $errors['images'][] = 'unsupproted file extension';
                }
            }

            if (count($errors)) {
                throw new HttpErrorException('Images error', $errors, 422);
            }
        }
    }
}
