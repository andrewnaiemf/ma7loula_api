<?php

namespace Modules\Vendor\Services;

use App\Enums\OrderVendorLineStatus;
use App\Jobs\SendCustomerOfferPushJob;
use App\Models\OrderVendor;
use Modules\Core\Exceptions\HttpErrorException;

class VendorOrderLineActionService
{
    public function acceptAtListedPrice(int $orderVendorId, int $vendorId): OrderVendor
    {
        $line = $this->lineForVendor($orderVendorId, $vendorId);

        if (! in_array($line->status, [OrderVendorLineStatus::New->value, OrderVendorLineStatus::OfferDeclined->value], true)) {
            throw new HttpErrorException(__('This line cannot be accepted at the listed price in its current state.'), [], 422);
        }

        $line->update([
            'status' => OrderVendorLineStatus::VendorAccepted->value,
            'offered_total' => null,
        ]);

        return $line->fresh(['order']);
    }

    public function submitCounterOffer(int $orderVendorId, int $vendorId, string $offeredTotal): OrderVendor
    {
        $line = $this->lineForVendor($orderVendorId, $vendorId);

        if (! in_array($line->status, [OrderVendorLineStatus::New->value, OrderVendorLineStatus::OfferDeclined->value], true)) {
            throw new HttpErrorException(__('You cannot send a price offer for this line in its current state.'), [], 422);
        }

        $line->update([
            'status' => OrderVendorLineStatus::OfferPending->value,
            'offered_total' => $offeredTotal,
        ]);

        SendCustomerOfferPushJob::dispatch($line->id);

        return $line->fresh(['order']);
    }

    private function lineForVendor(int $orderVendorId, int $vendorId): OrderVendor
    {
        $line = OrderVendor::query()
            ->whereKey($orderVendorId)
            ->where('vendor_id', $vendorId)
            ->whereNull('deleted_at')
            ->first();

        if (! $line) {
            throw new HttpErrorException(__('Order line not found or does not belong to this vendor.'), [], 404);
        }

        return $line;
    }
}
