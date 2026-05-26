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
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'order_vendor_id' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $exists = OrderVendor::query()
                        ->whereKey($value)
                        ->whereNull('deleted_at')
                        ->whereHas('order', fn ($q) => $q->where('user_id', Auth::id())->whereNull('deleted_at'))
                        ->exists();

                    if (! $exists) {
                        $fail(__('This order line was not found or does not belong to your account.'));
                    }
                },
            ],
            'action' => ['required', 'string', Rule::in(['accept', 'reject'])],
        ];
    }
}
