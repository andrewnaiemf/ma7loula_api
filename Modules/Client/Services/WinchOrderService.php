<?php

namespace Modules\Client\Services;

use App\Jobs\SendVendorNewOrderPushJob;
use App\Models\Order;
use App\Models\OrderWinch;
use App\Models\Setting;
use App\Models\User;
use App\Models\UserCar;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Client\Jobs\SendWinchOrderRequests;
use Modules\Core\Services\OrderFcmNotifier;
use Modules\Client\Requests\WinchOrder\AcceptWinchOffer;
use Modules\Client\Requests\WinchOrder\CalculateWinchOrderPriceRequest;
use Modules\Client\Requests\WinchOrder\CreateWinchOrderRequest;
use Modules\Client\Requests\WinchOrder\ListWinchDriverOffers;
use Modules\Client\Resources\WinchOrder\WinchOrderOfferResource;
use Modules\Client\Resources\WinchOrder\WinchOrderResource;

class WinchOrderService extends OrderService
{
    private ?User $user;

    public function __construct()
    {
        $this->user = Auth::user();
        parent::__construct();
    }


    public function calculatePrice(CalculateWinchOrderPriceRequest $request)
    {
        $distance_in_meters = $request->input("distance_in_meters");

         $distance_in_km = $distance_in_meters / 1000;

        $tax = Setting::first()?->tax_percentage ?? 10;
        $pricePerKilo = $this->getPricePerKilo();
        $billableKilo = $distance_in_km <= 10 ? 10 : ceil($distance_in_km);
        $base = $billableKilo * $pricePerKilo;

        return $base + (($tax / 100) * $base);
    }

    public function listWinchOrders()
    {
        $orders = Order::with([
            'vendors',
            'workers',
            'user_car',
            'user_car.car',
            'user_car.car.model',
            'user_car.car.model.brand',
            'user_car.client',
        ])
            ->where('user_id', $this->user->id)
            ->where('type', 'winch')
            ->orderBy('id', 'desc');

        return $orders;
    }


    public function createWinchOrder(CreateWinchOrderRequest $request)
    {
        $tax = Setting::first()?->tax_percentage ?? 10;
        $pricePerKilo = $this->getPricePerKilo();

        $distance_in_meters = $request->input("distance_in_meters");

         $distance_in_km = (double)$distance_in_meters / 1000;

        $billableKilo = $distance_in_km <= 10 ? 10 : ceil($distance_in_km);
        $services_price = $billableKilo * $pricePerKilo;
        $total_with_tax = $services_price + (($tax / 100) * $services_price);
        

        $now = Carbon::now();
        $duration = $request->input('duration_in_minutes');

        $delivery_time = $now->copy()->addMinutes($duration);

        $order_data = [
            'user_car_id' => $request->input('user_car_id'),
            'address_id' => $request->input('address_id'),
            'payment_method' => $request->input('payment_method'),
            'user_id' => $this->user->id,
            'delivery_time' => $delivery_time,
            'status' => 'new',
            'type' =>  'winch',
            'car_id' => UserCar::find($request->input('user_car_id'))->car_id,
            'products_price' => 0,
            'services_price' => $services_price,
            //'tax_price' => 0,
            'tax_price' => $services_price * ($tax / 100),
            'delivery_price' => 0,
            //'total' => $request->input('price')
            'total' => $total_with_tax
        ];

        /** @var Order $order */
        $order = Order::create($order_data);

        $orderWinch = OrderWinch::create([
            'order_id' => $order->id,
            'distance_in_meters' => $request->input('distance_in_meters'),
            'duration_in_minutes' => $request->input('duration_in_minutes'),

            'from_lat' => $request->input('from_lat'),
            'from_lon' => $request->input('from_lon'),
            'from_text' => $request->input('from_text'),

            'to_lat' => $request->input('to_lat'),
            'to_lon' => $request->input('to_lon'),
            'to_text' => $request->input('to_text'),

        ]);

        SendWinchOrderRequests::dispatchSync($orderWinch);

        $workers = Worker::query()
            ->where('type', 'winch')
            ->whereNull('deleted_at')
            ->get(['id', 'vendor_id']);

        $vendorIds = $workers->pluck('vendor_id')->filter()->unique()->values()->all();
        $workerIds = $workers->pluck('id')->all();

        if ($vendorIds !== []) {
            $this->ensureServiceOrderVendorStubs($order, $vendorIds);
            SendVendorNewOrderPushJob::dispatch($vendorIds, $order->id, 'winch');
        }

        if ($workerIds !== []) {
            app(OrderFcmNotifier::class)->notifyWorkersNewServiceRequest($order, 'winch', $workerIds);
        } else {
            Log::info('Winch order: no winch workers for FCM', ['order_id' => $order->id]);
        }

        return new WinchOrderResource($order);
    }

    //for developing
    public function sendFakeOffer(Request $request)
    {
        $worker = Worker::with(['vendor', 'user'])->where('type', 'winch')->inRandomOrder()->first();

        $order_offers_keys = 'winch_order_offers_' . $request->input('order_id');
        $worker_offer_key = 'winch_order_offers_' . $request->input('order_id') . '_' . $worker->id;

        if (!Cache::has($worker_offer_key)) {

            $new_offer = (new WinchOrderOfferResource($worker));

            if (Cache::has($order_offers_keys)) {
                $offers = Cache::get($order_offers_keys);
            } else {
                $offers = [];
            }

            array_unshift($offers, $worker_offer_key);

            Cache::put($order_offers_keys, $offers, 900);
            Cache::put($worker_offer_key, $new_offer, 900);
        }

        return $offers;
    }

    public function listWinchDriversOffers(Request $request)
    {
        $order_offers_keys = 'winch_order_offers_' . $request->input('order_id');

        $offers = [];
        if (Cache::has($order_offers_keys)) {
            $offers_keys = Cache::get($order_offers_keys);
           
            foreach ($offers_keys as $offer_key) {
                if (Cache::has($offer_key)) {
                    // dd($offer_key);
                    $offers[] = Cache::get($offer_key);
                }
            }
        }

        return $offers;
    }

    public function acceptOffer(AcceptWinchOffer $request)
    {
        $order = Order::find($request->input('order_id'));
        $accepted_offer_key = 'accepted_winch_request_' . $request->input('worker_id');

        if(!Cache::has($accepted_offer_key)){
            OrderWinch::where('order_id', $order->id)->update([
                'vendor_id' => $request->input('vendor_id'),
                'worker_id' => $request->input('worker_id')
            ]);

            $this->recordServiceOrderAcceptedVendor(
                $order,
                (int) $request->input('vendor_id'),
                (int) $request->input('worker_id')
            );

            $worker = Worker::find($request->input('worker_id'));
            if ($worker) {
                app(OrderFcmNotifier::class)->notifyWorkerOfferDecision($order, $worker, 'accept');
            }

            $order_res = new WinchOrderResource($order);

            Cache::put($accepted_offer_key, $order_res, 900);

            return $order_res;
        }

        return null;
    }

    public function rejectOffer(AcceptWinchOffer $request)
    {
        $worker_offer_key = 'winch_order_offers_' . $request->input('order_id') . '_' . $request->input('worker_id');

        Cache::forget($worker_offer_key);

        $order = Order::find($request->input('order_id'));
        $worker = Worker::find($request->input('worker_id'));
        if ($order && $worker) {
            app(OrderFcmNotifier::class)->notifyWorkerOfferDecision($order, $worker, 'reject');
        }

        return $this->listWinchDriversOffers($request);
    }

    private function getPricePerKilo(): float
    {
        $setting = Setting::first();
        $price = (float) ($setting?->price_per_kilo ?? 30);

        return $price > 0 ? $price : 30;
    }
}
