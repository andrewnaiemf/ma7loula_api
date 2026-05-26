<?php

namespace App\Jobs;

use App\Models\OrderVendor;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Modules\Core\Services\FcmPushService;

class SendVendorOfferDecisionPushJob
{
    use Dispatchable;

    public function __construct(
        public int $orderVendorId,
        public string $decision
    ) {}

    public function handle(FcmPushService $fcm): void
    {
        $line = OrderVendor::query()
            ->with(['order', 'vendor.user'])
            ->find($this->orderVendorId);

        if (! $line || ! $line->vendor || ! $line->vendor->user || ! $line->order) {
            return;
        }

        $vendorUser = $line->vendor->user;
        $tokens = $vendorUser->fcmTokens()->pluck('token')->filter()->unique()->values();

        if ($tokens->isEmpty() && ! empty($vendorUser->fcm_token)) {
            $tokens = collect([$vendorUser->fcm_token]);
        }

        if ($tokens->isEmpty()) {
            Log::info('Vendor offer decision push skipped: no FCM token', [
                'order_id' => $line->order_id,
                'order_vendor_id' => $line->id,
                'vendor_user_id' => $vendorUser->id,
                'decision' => $this->decision,
            ]);

            return;
        }

        $accepted = $this->decision === 'accept';
        $title = $accepted ? 'Offer accepted' : 'Offer rejected';
        /** Vendor-facing number: order_vendors.id (not orders.id). */
        $vendorOrderNo = (string) $line->id;
        $body = $accepted
            ? 'Customer accepted your offer for order #'.$vendorOrderNo
            : 'Customer rejected your offer for order #'.$vendorOrderNo;

        $offeredTotal = (string) $line->total;

        foreach ($tokens as $deviceToken) {
            $eventType = $accepted ? 'customer_accepted_vendor_offer' : 'customer_rejected_vendor_offer';
            $fcm->sendToToken(
                (string) $deviceToken,
                $title,
                $body,
                [
                    'title' => $title,
                    'body' => $body,
                    'offered_total' => $offeredTotal,
                    'event_type' => $eventType,
                    'action_required_for' => 'vendor',
                    'kind' => 'vendor_offer_decision',
                    'decision' => $this->decision,
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
