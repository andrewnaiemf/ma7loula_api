<?php

namespace Modules\Client\Services;

use App\Models\Order;
use App\Models\OrderEmergency;
use App\Models\OrderVendor;
use App\Models\OrderWinch;
use App\Models\User;
use App\Models\UserCar;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Client\Requests\EmergencyOrder\CreateEmergencyOrderRequest;
use Modules\Client\Requests\WinchOrder\AcceptWinchOffer;
use Modules\Client\Requests\WinchOrder\CalculateWinchOrderPriceRequest;
use Modules\Client\Requests\WinchOrder\CreateWinchOrderRequest;
use Modules\Client\Requests\WinchOrder\ListWinchDriverOffers;
use Modules\Client\Resources\EmergencyOrder\EmergencyOrderResource;
use Modules\Client\Resources\WinchOrder\WinchOrderOfferResource;
use Modules\Client\Resources\WinchOrder\WinchOrderResource;

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
        $order_data = [
            'user_car_id' => $request->input('user_car_id'),
            'user_id' => $this->user->id,
            'status' => 'new',
            'type' =>  'emergency',
            'car_id' => UserCar::find($request->input('user_car_id'))->car_id,
            'products_price' => 0,
            'services_price' => 300,
            'tax_price' => 0,
            'delivery_price' => 0,
            'total' => 300
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

        OrderEmergency::create([
            'order_id' => $order->id,
            'description' => $request->input('description'),
            'record' => $record
        ]);

        return new EmergencyOrderResource($order);
    }

    //for developing
    /* public function sendFakeOffer(Request $request)
    {
        $key = 'winch_order_offers_' . $request->input('order_id');
        $worker = Worker::with(['vendor', 'user'])->where('type', 'winch')->inRandomOrder()->first();

        $new_offer = (new WinchOrderOfferResource($worker));

        if (Cache::has($key)) {
            $offers = Cache::get($key);
        } else {
            $offers = [];
        }

        array_unshift($offers, $new_offer);

        $offers = json_decode(json_encode($offers));

        Cache::put($key, $offers, 60);

        return $offers;
    }

    public function listWinchDriversOffers(ListWinchDriverOffers $request)
    {
        $key = 'winch_order_offers_' . $request->input('order_id');

        if (Cache::has($key)) {
            $offers = Cache::get($key);
            return array_values(array_filter($offers, function ($item) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $item->expires_at) > Carbon::now();
            }));
        }

        return [];
    }

    public function acceptOffer(AcceptWinchOffer $request){
        $order = Order::find($request->input('order_id'));

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

        return new WinchOrderResource($order);
    } */
}
