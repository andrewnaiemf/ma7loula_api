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

class SendCustomerOrderStatusPushJob implements ShouldQueue
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
            Log::info('Customer order status push skipped: no FCM token', [
                'order_id' => $line->order_id,
                'order_vendor_id' => $line->id,
                'customer_user_id' => $customer->id,
                'status' => $line->status,
            ]);

            return;
        }

        $title = 'Order status updated';
        $body = 'Order #'.$line->order_id.' status changed to '.$line->status;

        foreach ($tokens as $deviceToken) {
            $fcm->sendToToken(
                (string) $deviceToken,
                $title,
                $body,
                [
                    'event_type' => 'vendor_updated_order_status',
                    'action_required_for' => 'customer',
                    'kind' => 'order_status_updated',
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
