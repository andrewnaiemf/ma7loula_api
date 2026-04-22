<?php

namespace Modules\Vendor\Requests\CarParts;

use App\Enums\OrderVendorLineStatus;
use App\Models\OrderVendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\Request;

class UpdateOrderStatusRequest extends Request
{
    public function authorize(): bool
    {
        return Auth::user()
            && Auth::user()->vendor
            && OrderVendor::where('id', $this->input('id'))->where('vendor_id', Auth::user()->vendor->id)->exists();
    }

    public function rules(): array
    {
        $vendor = Auth::user()->vendor;

        return [
            'id' => [
                'required',
                Rule::exists('order_vendors', 'id')->where('vendor_id', $vendor->id)->whereNull('deleted_at'),
            ],
            'status' => [
                'required',
                'string',
                Rule::in(OrderVendorLineStatus::values()),
            ],
        ];
    }
}
