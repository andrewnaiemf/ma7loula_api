<?php

namespace Modules\Vendor\Requests\BT\Vendor;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;
use Illuminate\Validation\Rule;
use Modules\Core\Exceptions\HttpErrorException;

class AddTireRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->vendor;
    }

    public function rules(): array
    {
        $rules =  [
            'name' => ['required', 'string'],
            'sku' => ['required', 'string'],
            'description' => ['required', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'string'],
            'default_image' => ['nullable', 'string'],
            'brand_id' => ['required',  Rule::exists('product_brands', 'id')->whereNull('deleted_at')->where('product_category_id', Product::TiresCategory)],
            'stock' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'price_before_discount' => ['nullable', 'numeric'],
            'car_ids' => ['required', 'array'],
            'car_ids.*' => ['required', Rule::exists('cars', 'id')->whereNull('deleted_at')],
            'sku' => ['required', 'string'],

            'year_of_manufacture' => ['required', 'date_format:Y'],
            'tire_type' => ['required', 'string', 'in:flat,normal'],
            'height' => ['required', 'integer'],
            'width' => ['required', 'integer'],
            'length' => ['required', 'integer'],
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
