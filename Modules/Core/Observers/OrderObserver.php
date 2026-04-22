<?php

namespace Modules\Core\Observers;

use App\Enums\OrderStatus;
use App\Enums\OrderVendorLineStatus;
use App\Models\Order;
use App\Models\OrderVendor;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        $order->statuses()->create([
            'status' => OrderStatus::New->value,
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $order->statuses()->create([
                'status' => $order->status,
            ]);

            $terminal = [OrderStatus::Completed->value, OrderStatus::Cancelled->value];
            if (! in_array($order->status, $terminal, true)) {
                return;
            }

            $lineStatus = $order->status === OrderStatus::Completed->value
                ? OrderVendorLineStatus::Completed->value
                : OrderVendorLineStatus::Cancelled->value;

            OrderVendor::withoutEvents(function () use ($order, $lineStatus) {
                $order->vendor_orders()->update([
                    'status' => $lineStatus,
                ]);
            });
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        // ...
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        // ...
    }

    /**
     * Handle the Order "forceDeleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        // ...
    }
}
