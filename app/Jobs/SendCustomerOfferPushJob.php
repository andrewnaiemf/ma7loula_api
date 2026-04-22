<?php

namespace App\Jobs;

use App\Models\OrderVendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FcmPushService;

class SendCustomerOfferPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $orderVendorId
    ) {}

    public function handle(FcmPushService $fcm): void
    {
        $line = OrderVendor::query()
            ->with(['order.user'])
            ->find($this->orderVendorId);

        if (! $line || ! $line->order || ! $line->order->user) {
            return;
        }

        $customer = $line->order->user;
        $tokens = $customer->fcmTokens()->pluck('token')->filter()->unique()->values();

        if ($tokens->isEmpty() && ! empty($customer->fcm_token)) {
            $tokens = collect([$customer->fcm_token]);
        }

        if ($tokens->isEmpty()) {
            Log::info('Customer offer push skipped: no FCM token', [
                'order_id' => $line->order_id,
                'order_vendor_id' => $line->id,
                'customer_user_id' => $customer->id,
            ]);

            return;
        }

        foreach ($tokens as $deviceToken) {
            $fcm->sendToToken(
                (string) $deviceToken,
                'New vendor offer',
                'A vendor sent a new price offer for order #'.$line->order_id,
                [
                    'kind' => 'vendor_offer_created',
                    'order_id' => (string) $line->order_id,
                    'order_vendor_id' => (string) $line->id,
                    'type' => (string) $line->order->type,
                    'offered_total' => (string) ($line->offered_total ?? ''),
                    'vendor_id' => (string) $line->vendor_id,
                ]
            );
        }
    }
}
