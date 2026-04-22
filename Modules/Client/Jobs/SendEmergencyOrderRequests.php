<?php

namespace Modules\Client\Jobs;

use App\Models\OrderEmergency;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Client\Resources\EmergencyOrder\EmergencyOrderResource;

class SendEmergencyOrderRequests implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private OrderEmergency $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $winch_order = $this->order;
        $order = $winch_order->order;
        $currentLatitude = $winch_order->lat;
        $currentLongitude = $winch_order->lon;

        $workers = DB::table('workers')
            ->selectRaw('*, ( 6371 * acos( cos( radians(?) ) * cos( radians( lat ) ) * cos( radians( lon ) - radians(?) ) + sin( radians(?) ) * sin( radians( lat ) ) ) ) AS distance', [$currentLatitude, $currentLongitude, $currentLatitude])
            ->where('type', 'emergency')
            ->having('distance', '<', 10)
            ->orderBy('distance', 'asc')
            ->get();

        $order_res = new EmergencyOrderResource($order);

        foreach ($workers as $worker) {
            $worker_key = 'emergency_request_' . $worker->id;
            $order_key = 'emergency_request_' . $worker->id . '_' . $order->id;

            if (!Cache::has($order_key)) {

                if (Cache::has($worker_key)) {
                    $requests_keys = Cache::get($worker_key);
                } else {
                    $requests_keys = [];
                }

                array_unshift($requests_keys, $order_key);
                Cache::put($order_key, $order_res, 900);
                Cache::put($worker_key, $requests_keys, 900);
            }
        }
    }
}
