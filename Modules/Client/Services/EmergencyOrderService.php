<?php

namespace Modules\Client\Services;

use App\Jobs\SendVendorNewOrderPushJob;
use App\Models\Order;
use App\Models\OrderEmergency;
use App\Models\User;
use App\Models\UserCar;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Modules\Client\Jobs\SendEmergencyOrderRequests;
use Modules\Core\Services\OrderFcmNotifier;
use Modules\Client\Requests\EmergencyOrder\CreateEmergencyOrderRequest;
use Modules\Client\Requests\WinchOrder\AcceptWinchOffer;
use Modules\Client\Resources\EmergencyOrder\EmergencyOrderResource;
use Modules\Client\Resources\WinchOrder\WinchOrderOfferResource;

class EmergencyOrderService extends OrderService
{
    private ?User $user;

    public function __construct()
    {
        $this->user = Auth::user();
        parent::__construct();
    }

    public function createEmergencyOrder(CreateEmergencyOrderRequest $request)
    {
        $products_price = 300;
        $tax = \App\Models\Setting::first()?->tax_percentage??10;
        $total_with_tax = $products_price + ($products_price * $tax / 100);
        $order_data = [
            'user_car_id' => $request->input('user_car_id'),
            'user_id' => $this->user->id,
            'status' => 'new',
            'type' =>  'emergency',
            'car_id' => UserCar::find($request->input('user_car_id'))->car_id,
            'products_price' => 0,
            'services_price' => 300,
           // 'tax_price' => 0,
           'tax_price' => $tax,
            'delivery_price' => 0,
           // 'total' => 300
           'total' => $total_with_tax
        ];

        /** @var Order $order */
        $order = Order::create($order_data);


        $record = null;
        if ($request->input('record')) {
            $filename = $request->input('record');
            $path = 'app/public/emergency_order/';

            if (!is_dir(storage_path($path))) {
                mkdir(storage_path($path));
            }

            $filepath = storage_path('app/public/temp/' . $filename);
            $fileNewPath = storage_path($path . $filename);
            if (file_exists($filepath)) {
                rename($filepath, $fileNewPath);
                $record = 'storage/emergency_order/' . $filename;
            }
        }

        $orderEmergency = OrderEmergency::create([
            'order_id' => $order->id,
            'description' => $request->input('description'),
            'record' => $record,
            'lat' => $request->input('lat'),
            'lon' => $request->input('lon'),
            'location' => $request->input('location')
        ]);

        SendEmergencyOrderRequests::dispatchSync($orderEmergency);

        $workers = Worker::query()
            ->where('type', 'emergency')
            ->whereNull('deleted_at')
            ->get(['id', 'vendor_id']);

        $vendorIds = $workers->pluck('vendor_id')->filter()->unique()->values()->all();
        $workerIds = $workers->pluck('id')->all();

        if ($vendorIds !== []) {
            $this->ensureServiceOrderVendorStubs($order, $vendorIds);
            SendVendorNewOrderPushJob::dispatch($vendorIds, $order->id, 'emergency');
        }

        if ($workerIds !== []) {
            app(OrderFcmNotifier::class)->notifyWorkersNewServiceRequest($order, 'emergency', $workerIds);
        } else {
            Log::info('Emergency order: no emergency workers for FCM', ['order_id' => $order->id]);
        }

        return new EmergencyOrderResource($order);
    }

    //for developing
    public function sendFakeOffer(Request $request)
    {
        $key = 'emergency_order_offers_' . $request->input('order_id');
        $worker = Worker::with(['vendor', 'user'])->where('type', 'emergency')->inRandomOrder()->first();

        $new_offer = (new WinchOrderOfferResource($worker));

        if (Cache::has($key)) {
            $offers = Cache::get($key);
        } else {
            $offers = [];
        }

        array_unshift($offers, $new_offer);

        $offers = json_decode(json_encode($offers));

        Cache::put($key, $offers, 900);

        return $offers;
    }



    public function listOffers(Request $request)
    {
        $key = 'emergency_order_offers_' . $request->input('order_id');

        $offers = [];

        if (Cache::has($key)) {
            $offers_keys = Cache::get($key);
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
        $accepted_offer_key = 'accepted_emergency_request_' . $request->input('worker_id');

        if (!Cache::has($accepted_offer_key)) {
            OrderEmergency::where('order_id', $order->id)->update([
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

            $order_res =  new EmergencyOrderResource($order);

            Cache::put($accepted_offer_key, $order_res, 900);

            return $order_res;
        }
        return null;
    }

    public function rejectOffer(AcceptWinchOffer $request)
    {
       // dd($request->all());
        $cache_key = 'emergency_order_offers_' . $request->input('order_id');
       // dd($cache_key);
       // dd(Cache::get($cache_key));
        if (Cache::has($cache_key)) {
            $offers = Cache::get($cache_key);
        } else {
            $offers = [];
        }
    //dd($offers);
        foreach ($offers as $key => $offer) {
            $offer_arr = explode('_',$offer);
            if (count($offer_arr) > 0 && $offer_arr[count($offer_arr) - 1] == $request->input('worker_id')) {
                unset($offers[$key]);
            }
        }

        $offers = json_decode(json_encode($offers));

        Cache::put($cache_key, (array) $offers, 900);

        $order = Order::find($request->input('order_id'));
        $worker = Worker::find($request->input('worker_id'));
        if ($order && $worker) {
            app(OrderFcmNotifier::class)->notifyWorkerOfferDecision($order, $worker, 'reject');
        }

        return $this->listOffers($request);
    }

    public function listEmergencyOrders()
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
            ->where('type', 'emergency')
            ->orderBy('id', 'desc');

        return $orders;
    }
}
