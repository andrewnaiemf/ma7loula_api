<?php

namespace Modules\Client\Services;

use App\Models\Order;
use App\Models\OrderWinch;
use App\Models\User;
use App\Models\UserCar;
use App\Models\Worker;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Events\WinchOrder\WorkerAcceptedOrder;
use Modules\Client\Requests\WinchOrder\CalculateWinchOrderPriceRequest;
use Modules\Client\Requests\WinchOrder\CreateWinchOrderRequest;
use Modules\Client\Resources\WinchOrder\WinchOrderResource;

class WinchOrderService extends OrderService
{
    private ?User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }


    public function calculatePrice(CalculateWinchOrderPriceRequest $request)
    {
        return $request->input("distance_in_meters") * 0.50;
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

        OrderWinch::create([
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

        //for testing
        $this->sendFakeOffers($order);

        return new WinchOrderResource($order);
    }

    private function sendFakeOffers($order){

        $workers = Worker::where('type', 'winch')->take(3)->get();

        foreach($workers as $worker){
            WorkerAcceptedOrder::dispatch($order->user, $order, $worker);
        }
    }

}
