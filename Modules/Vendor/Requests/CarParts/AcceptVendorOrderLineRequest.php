<?php

namespace Modules\Vendor\Requests\CarParts;

use App\Models\OrderVendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\Request;

class AcceptVendorOrderLineRequest extends Request
{
    public function authorize(): bool
    {
        return Auth::user()
            && Auth::user()->vendor
            && OrderVendor::query()
                ->whereKey($this->input('order_vendor_id'))
                ->where('vendor_id', Auth::user()->vendor->id)
                ->exists();
    }

    public function rules(): array
    {
        $vendorId = Auth::user()->vendor->id;

        return [
            'order_vendor_id' => [
                'required',
                Rule::exists('order_vendors', 'id')->where('vendor_id', $vendorId)->whereNull('deleted_at'),
            ],
        ];
    }
}
