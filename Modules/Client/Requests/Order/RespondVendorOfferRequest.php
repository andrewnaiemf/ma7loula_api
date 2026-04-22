<?php

namespace Modules\Client\Requests\Order;

use App\Models\OrderVendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class RespondVendorOfferRequest extends PublicRequest
{
    public function authorize(): bool
    {
        if (! Auth::check()) {
            return false;
        }

        return OrderVendor::query()
            ->whereKey($this->input('order_vendor_id'))
            ->whereHas('order', fn ($q) => $q->where('user_id', Auth::id())->whereNull('deleted_at'))
            ->exists();
    }

    public function rules(): array
    {
        return [
            'order_vendor_id' => ['required', 'integer'],
            'action' => ['required', 'string', Rule::in(['accept', 'reject'])],
        ];
    }
}
