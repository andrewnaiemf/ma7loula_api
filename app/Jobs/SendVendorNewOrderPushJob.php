<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderVendor;
use App\Models\Vendor;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FcmPushService;

/**
 * Sends FCM to vendor account users for a new order.
 * Runs synchronously (no queue worker required) so pushes are reliable from HTTP requests.
 */
class SendVendorNewOrderPushJob
{
    use Dispatchable;

    public function __construct(
        public array $vendorIds,
        public int $orderId,
        public string $orderType
    ) {}

    public function handle(FcmPushService $fcm): void
    {
        if ($this->vendorIds === []) {
            return;
        }

        $vendors = Vendor::query()
            ->whereIn('id', $this->vendorIds)
            ->with(['user.fcmTokens'])
            ->get();

        foreach ($vendors as $vendor) {
            $user = $vendor->user;
            if (! $user) {
                continue;
            }

            $tokens = $user->fcmTokens->pluck('token')->filter()->unique()->values();
            if ($tokens->isEmpty() && ! empty($user->fcm_token)) {
                $tokens = collect([$user->fcm_token]);
            }

            if ($tokens->isEmpty()) {
                Log::info('Vendor new order push skipped: no FCM token', [
                    'vendor_id' => $vendor->id,
                    'user_id' => $user->id,
                    'order_id' => $this->orderId,
                ]);

                continue;
            }

            foreach ($tokens as $deviceToken) {
                $line = OrderVendor::query()
                    ->where('order_id', $this->orderId)
                    ->where('vendor_id', $vendor->id)
                    ->whereNull('deleted_at')
                    ->first();

                $orderVendorId = $line?->id;
                /** Shown to vendor in notification (order_vendors.id); fallback if line missing. */
                $vendorOrderNo = (string) ($orderVendorId ?? $this->orderId);

                $title = match ($this->orderType) {
                    'winch', 'emergency' => 'New service request',
                    default => 'New order',
                };

                $body = match ($this->orderType) {
                    'winch' => 'You have a new winch request #'.$vendorOrderNo,
                    'emergency' => 'You have a new emergency request #'.$vendorOrderNo,
                    default => 'You have a new order #'.$vendorOrderNo,
                };

                $offeredTotal = $line !== null
                    ? (string) $line->total
                    : (string) (Order::query()->whereKey($this->orderId)->value('total') ?? '');

                $data = [
                    'title' => $title,
                    'body' => $body,
                    'offered_total' => $offeredTotal,
                    'event_type' => 'new_order_created',
                    'action_required_for' => 'vendor',
                    'order_id' => (string) $this->orderId,
                    'order_vendor_id' => $orderVendorId !== null ? (string) $orderVendorId : '',
                    'status' => 'new',
                    'type' => $this->orderType,
                    'vendor_id' => (string) $vendor->id,
                ];

                $ok = $fcm->sendToToken(
                    (string) $deviceToken,
                    $title,
                    $body,
                    $data
                );

                if (! $ok) {
                    Log::debug('FCM not sent for vendor device', ['vendor_id' => $vendor->id]);
                }
            }
        }
    }
}
