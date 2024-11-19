<?php

namespace Modules\Core\Observers;

use App\Models\OrderVendor;

class OrderVendorObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(OrderVendor $order): void
    {
        $order->statuses()->create([
            'status' => 'new'
        ]);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(OrderVendor $order_vendor): void
    {
        if ($order_vendor->wasChanged('status')) {
            $main_order = $order_vendor->order;
            $vendor_orders = $main_order->vendor_orders;
            $statuses =  $vendor_orders->groupBy('status')->toArray();
            if(count($statuses) == 1){
                $main_order->update([
                    'status' => $order_vendor->status
                ]);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(OrderVendor $order): void
    {
        // ...
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(OrderVendor $order): void
    {
        // ...
    }

    /**
     * Handle the Order "forceDeleted" event.
     */
    public function forceDeleted(OrderVendor $order): void
    {
        // ...
    }
}
