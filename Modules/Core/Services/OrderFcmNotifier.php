<?php

namespace Modules\Core\Services;

use App\Models\Order;
use App\Models\OrderVendor;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OrderFcmNotifier
{
    public function __construct(private FcmPushService $fcm) {}

    /**
     * @return Collection<int, string>
     */
    public function tokensForUser(User $user): Collection
    {
        $tokens = $user->fcmTokens()->pluck('token')->filter()->unique()->values();

        if ($tokens->isEmpty() && ! empty($user->fcm_token)) {
            return collect([$user->fcm_token]);
        }

        return $tokens;
    }

    public function sendToUser(User $user, string $title, string $body, array $data): void
    {
        $tokens = $this->tokensForUser($user);
        if ($tokens->isEmpty()) {
            Log::info('FCM skipped: user has no token', ['user_id' => $user->id, 'event_type' => $data['event_type'] ?? '']);

            return;
        }

        foreach ($tokens as $deviceToken) {
            $this->fcm->sendToToken((string) $deviceToken, $title, $body, $data);
        }
    }

    /**
     * New winch / emergency request for driver (worker) apps — uses orders.id in message.
     *
     * @param  list<int>  $workerIds
     */
    public function notifyWorkersNewServiceRequest(Order $order, string $type, array $workerIds): void
    {
        if ($workerIds === []) {
            return;
        }

        $workers = Worker::query()
            ->whereIn('id', $workerIds)
            ->with('user')
            ->get();

        $orderNo = (string) $order->id;
        $offeredTotal = (string) $order->total;

        $title = match ($type) {
            'emergency' => 'New emergency request',
            default => 'New winch request',
        };

        $body = match ($type) {
            'emergency' => 'You have a new emergency request #'.$orderNo,
            default => 'You have a new winch request #'.$orderNo,
        };

        foreach ($workers as $worker) {
            if (! $worker->user) {
                continue;
            }

            $this->sendToUser($worker->user, $title, $body, [
                'title' => $title,
                'body' => $body,
                'offered_total' => $offeredTotal,
                'event_type' => 'new_service_request',
                'action_required_for' => 'worker',
                'kind' => 'new_order_created',
                'order_id' => $orderNo,
                'status' => (string) $order->status,
                'type' => $type,
                'worker_id' => (string) $worker->id,
                'vendor_id' => (string) $worker->vendor_id,
            ]);
        }
    }

    /** Worker sent offer — notify customer (orders.id in message). */
    public function notifyCustomerServiceOffer(Order $order, Worker $worker): void
    {
        if (! $order->user) {
            $order->load('user');
        }
        if (! $order->user) {
            return;
        }

        $orderNo = (string) $order->id;
        $type = (string) $order->type;
        $offeredTotal = (string) $order->total;

        $title = 'New driver offer';
        $body = match ($type) {
            'emergency' => 'A provider sent an offer for emergency request #'.$orderNo,
            default => 'A driver sent an offer for winch request #'.$orderNo,
        };

        $this->sendToUser($order->user, $title, $body, [
            'title' => $title,
            'body' => $body,
            'offered_total' => $offeredTotal,
            'event_type' => 'worker_offer_created',
            'action_required_for' => 'customer',
            'kind' => 'worker_offer_created',
            'order_id' => $orderNo,
            'status' => (string) $order->status,
            'type' => $type,
            'worker_id' => (string) $worker->id,
            'vendor_id' => (string) $worker->vendor_id,
        ]);
    }

    /** Customer accepted or rejected worker offer. */
    public function notifyWorkerOfferDecision(Order $order, Worker $worker, string $decision): void
    {
        if (! $worker->user) {
            $worker->load('user');
        }
        if (! $worker->user) {
            return;
        }

        $accepted = $decision === 'accept';
        $orderNo = (string) $order->id;
        $type = (string) $order->type;

        $title = $accepted ? 'Offer accepted' : 'Offer rejected';
        $body = $accepted
            ? 'Customer accepted your offer for request #'.$orderNo
            : 'Customer rejected your offer for request #'.$orderNo;

        $this->sendToUser($worker->user, $title, $body, [
            'title' => $title,
            'body' => $body,
            'offered_total' => (string) $order->total,
            'event_type' => $accepted ? 'customer_accepted_worker_offer' : 'customer_rejected_worker_offer',
            'action_required_for' => 'worker',
            'kind' => 'worker_offer_decision',
            'decision' => $decision,
            'order_id' => $orderNo,
            'status' => (string) $order->status,
            'type' => $type,
            'worker_id' => (string) $worker->id,
            'vendor_id' => (string) $worker->vendor_id,
        ]);
    }

    /** Worker or vendor updated line status — notify customer. */
    public function notifyCustomerOrderStatusUpdate(OrderVendor $line): void
    {
        $line->loadMissing(['order.user']);
        if (! $line->order || ! $line->order->user) {
            return;
        }

        $orderNo = (string) $line->order_id;
        $title = 'Order status updated';
        $body = 'Order #'.$orderNo.' status changed to '.$line->status;
        $offeredTotal = (string) $line->total;

        $this->sendToUser($line->order->user, $title, $body, [
            'title' => $title,
            'body' => $body,
            'offered_total' => $offeredTotal,
            'event_type' => 'order_status_updated',
            'action_required_for' => 'customer',
            'kind' => 'order_status_updated',
            'order_id' => $orderNo,
            'status' => (string) $line->order->status,
            'order_vendor_status' => (string) $line->status,
            'order_vendor_id' => (string) $line->id,
            'type' => (string) $line->order->type,
            'vendor_id' => (string) $line->vendor_id,
        ]);
    }

    /** Customer cancelled or updated order — notify assigned worker if any. */
    public function notifyWorkerOrderStatusUpdate(Order $order, ?int $workerId = null): void
    {
        $workerId = $workerId ?? $this->assignedWorkerIdForOrder($order);
        if (! $workerId) {
            return;
        }

        $worker = Worker::with('user')->find($workerId);
        if (! $worker?->user) {
            return;
        }

        $orderNo = (string) $order->id;
        $type = (string) $order->type;
        $title = 'Order status updated';
        $body = 'Request #'.$orderNo.' status changed to '.$order->status;

        $this->sendToUser($worker->user, $title, $body, [
            'title' => $title,
            'body' => $body,
            'offered_total' => (string) $order->total,
            'event_type' => 'customer_updated_order_status',
            'action_required_for' => 'worker',
            'kind' => 'order_status_updated',
            'order_id' => $orderNo,
            'status' => (string) $order->status,
            'type' => $type,
            'worker_id' => (string) $worker->id,
            'vendor_id' => (string) $worker->vendor_id,
        ]);
    }

    private function assignedWorkerIdForOrder(Order $order): ?int
    {
        if ($order->type === 'winch') {
            return $order->winch_order?->worker_id
                ?? $order->winch_order()->value('worker_id');
        }
        if ($order->type === 'emergency') {
            return $order->emergency_order?->worker_id
                ?? $order->emergency_order()->value('worker_id');
        }

        return $order->vendor_orders()->whereNotNull('worker_id')->value('worker_id');
    }
}
