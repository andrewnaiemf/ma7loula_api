<?php

namespace Modules\Core\Observers;

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
            'status' => 'new'
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $order->statuses()->create([
                'status' => $order->status
            ]);

            OrderVendor::withoutEvents(function () use ($order) {
                $order->vendor_orders()->update([
                    'status' =>  $order->status
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
