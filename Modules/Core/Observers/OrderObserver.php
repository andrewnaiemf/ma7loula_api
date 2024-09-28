<?php

namespace Modules\Core\Observers;

use App\Models\Order;

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
