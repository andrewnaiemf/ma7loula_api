<?php

namespace Modules\Core\Observers;

use App\Enums\OrderStatus;
use App\Enums\OrderVendorLineStatus;
use App\Models\OrderVendor;
use Modules\Client\Services\OrderService;

class OrderVendorObserver
{
    public function created(OrderVendor $orderVendor): void
    {
        $this->afterLineChanged($orderVendor, false);
    }

    public function updated(OrderVendor $orderVendor): void
    {
        if ($orderVendor->wasChanged([
            'status',
            'offered_total',
            'products_price',
            'total',
            'delivery_price',
            'services_price',
            'tax_price',
        ])) {
            $resyncOrderTotals = $orderVendor->wasChanged(['products_price', 'total']);
            $this->afterLineChanged($orderVendor, $resyncOrderTotals);
        }
    }

    private function afterLineChanged(OrderVendor $line, bool $resyncOrderTotals): void
    {
        $order = $line->order;
        if (! $order) {
            return;
        }

        if ($resyncOrderTotals) {
            OrderService::syncAggregatesFromVendorLines($order);
        }

        $lines = $order->fresh()->vendor_orders()->get();
        $next = $this->deriveOrderStatus($lines);
        if ($next === null) {
            return;
        }

        $order->refresh();
        if ($next === $order->status) {
            return;
        }

        $order->update(['status' => $next]);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, OrderVendor>  $lines
     */
    private function deriveOrderStatus($lines): ?string
    {
        if ($lines->isEmpty()) {
            return null;
        }

        $status = fn (OrderVendor $l) => $l->status;

        if ($lines->every(fn ($l) => $status($l) === OrderVendorLineStatus::Completed->value)) {
            return OrderStatus::Completed->value;
        }
        if ($lines->every(fn ($l) => $status($l) === OrderVendorLineStatus::Cancelled->value)) {
            return OrderStatus::Cancelled->value;
        }
        if ($lines->every(fn ($l) => $status($l) === OrderVendorLineStatus::New->value)) {
            return OrderStatus::New->value;
        }
        if ($lines->contains(fn ($l) => $status($l) === OrderVendorLineStatus::OfferPending->value)) {
            return OrderStatus::PendingCustomer->value;
        }
        if ($lines->contains(fn ($l) => in_array($status($l), [
            OrderVendorLineStatus::New->value,
            OrderVendorLineStatus::OfferDeclined->value,
        ], true))) {
            return OrderStatus::PendingVendor->value;
        }
        if ($lines->every(fn ($l) => in_array($status($l), [
            OrderVendorLineStatus::VendorAccepted->value,
            OrderVendorLineStatus::Confirmed->value,
        ], true))) {
            return OrderStatus::Accepted->value;
        }
        if ($lines->contains(fn ($l) => $status($l) === OrderVendorLineStatus::Completed->value)) {
            return OrderStatus::Processing->value;
        }

        return OrderStatus::New->value;
    }

    public function deleted(OrderVendor $orderVendor): void
    {
        //
    }

    public function restored(OrderVendor $orderVendor): void
    {
        //
    }

    public function forceDeleted(OrderVendor $orderVendor): void
    {
        //
    }
}
