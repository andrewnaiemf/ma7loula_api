<?php

namespace Modules\Client\Services;

use App\Models\Order;
use App\Models\OrderVendor;
use App\Models\OrderWinch;
use App\Models\User;
use App\Models\UserCar;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Client\Jobs\SendWinchOrderRequests;
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
        return $request->input("distance_in_meters") * 0.50;
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
        $order_data = [
            'user_car_id' => $request->input('user_car_id'),
            'address_id' => $request->input('address_id'),
            'payment_method' => $request->input('payment_method'),
            'user_id' => $this->user->id,
            'status' => 'new',
            'type' =>  'winch',
            'car_id' => UserCar::find($request->input('user_car_id'))->car_id,
            'products_price' => 0,
            'services_price' => $request->input('price'),
            'tax_price' => 0,
            'delivery_price' => 0,
            'total' => $request->input('price')
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

        SendWinchOrderRequests::dispatch($orderWinch);

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

            Cache::put($order_offers_keys, $offers, 60);
            Cache::put($worker_offer_key, $new_offer, 60);
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

            OrderVendor::create(
                [
                    'order_id' => $request->input('order_id'),
                    'vendor_id' => $request->input('vendor_id'),
                    'worker_id' => $request->input('worker_id'),
                    'status' => $order->status,
                    'products_price' => $order->products_price,
                    'services_price' => $order->services_price,
                    'tax_price' => $order->tax_price,
                    'delivery_price' => $order->delivery_price,
                    'total' => $order->total,
                ]
            );

            $order_res = new WinchOrderResource($order);

            Cache::put($accepted_offer_key, $order_res, 60*10);

            return $order_res;
        }

        return null;
    }

    public function rejectOffer(AcceptWinchOffer $request)
    {
        $worker_offer_key = 'winch_order_offers_' . $request->input('order_id') . '_' . $request->input('worker_id');

        Cache::forget($worker_offer_key);

        return $this->listWinchDriversOffers($request);
    }
}
