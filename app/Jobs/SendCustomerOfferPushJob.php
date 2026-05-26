<?php

namespace App\Jobs;

use App\Models\OrderVendor;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FcmPushService;

class SendCustomerOfferPushJob
{
    use Dispatchable;

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

        $title = 'New vendor offer';
        $body = 'A vendor sent a new price offer for order #'.$line->order_id;
        $offeredTotal = (string) ($line->offered_total ?? '');

        foreach ($tokens as $deviceToken) {
            $fcm->sendToToken(
                (string) $deviceToken,
                $title,
                $body,
                [
                    'title' => $title,
                    'body' => $body,
                    'offered_total' => $offeredTotal,
                    'event_type' => 'vendor_offer_created',
                    'action_required_for' => 'customer',
                    'kind' => 'vendor_offer_created',
                    'order_id' => (string) $line->order_id,
                    'status' => (string) $line->order->status,
                    'order_vendor_status' => (string) $line->status,
                    'order_vendor_id' => (string) $line->id,
                    'type' => (string) $line->order->type,
                    'vendor_id' => (string) $line->vendor_id,
                ]
            );
        }
    }
}
