<?php

namespace App\Jobs;

use App\Models\Vendor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FcmPushService;

class SendVendorNewOrderPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
                $ok = $fcm->sendToToken(
                    (string) $deviceToken,
                    'New order',
                    'You have a new order #'.$this->orderId,
                    [
                        'order_id' => (string) $this->orderId,
                        'type' => $this->orderType,
                        'vendor_id' => (string) $vendor->id,
                    ]
                );

                if (! $ok) {
                    Log::debug('FCM not sent for vendor device', ['vendor_id' => $vendor->id]);
                }
            }
        }
    }
}
