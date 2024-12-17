<?php

namespace Modules\Winch\Observers;

use App\Models\Order;
use App\Models\Transaction;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->wasChanged('status') && $order->type == 'winch' && $order->status == 'completed') {
            if ($order->payment_method == 'visa') {
                $type = 'credit';
                $amount = $order->total * 90 / 100;
                $amountWithSign = $amount;
            } else {
                $type = 'debit';
                $amount = $order->total * 10 / 100;
                $amountWithSign = -1 * $amount;
            }

            $worker = $order->workers()->first();

            if ($worker) {

                $worker->update([
                    'balance' => $worker->balance + $amountWithSign
                ]);

                Transaction::updateOrCreate([
                    'order_id' => $order->id
                ], [
                    'user_id' => $worker->user_id,
                    'amount' =>  $amount,
                    'type' =>  $type,
                    'balance' => $worker->balance
                ]);
            }
        }
    }
}
