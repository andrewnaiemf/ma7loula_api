<?php

namespace Modules\Vendor\Requests\CarParts;

use App\Models\OrderVendor;
use Modules\Core\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->vendor && OrderVendor::where('id', $this->id)->where('vendor_id', Auth::user()->vendor->id)->exists();
    }


    public function rules(): array
    {
        $vendor = Auth::user()->vendor;
        return
            [
                'id' => [
                    'required', Rule::exists('orders', 'id')->whereNull('deleted_at'),  Rule::exists('order_vendors', 'id')->where('vendor_id', $vendor->id)->whereNull('deleted_at')
                ],
                'status' => [
                    'required', 'string'
                ]
            ];
    }
}
