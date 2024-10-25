<?php

namespace Modules\Vendor\Requests\BT\Vendor;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;
use Illuminate\Validation\Rule;


class ProductDetailsRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->vendor;
    }

    public function rules(): array
    {
        $rules =  [
            'id' => ['required', Rule::exists('products', 'id')->whereNull('deleted_at')->where('vendor_id', Auth::user()->vendor->id)],
        ];

        return $rules;
    }
}
