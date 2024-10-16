<?php

namespace Modules\Client\Services;

use App\Models\Order;
use App\Models\OrderRate;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCar;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Requests\CarParts\CarPartsOrderDetailsRequest;
use Modules\Client\Requests\Order\CreateOrderRequest;
use Modules\Client\Requests\Order\GetAvailableSoltsRequest;
use Modules\Client\Requests\Order\RateOrderRequest;
use Modules\Client\Requests\Order\UpdateOrderStatusRequest;
use Modules\Client\Resources\OrderCarPartsResource;
use Modules\Client\Resources\OrderResource;
use Modules\Client\Resources\WinchOrder\WinchOrderResource;

class OrderService
{
    private ?User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function createOrder(CreateOrderRequest $request, $type)
    {
        $products_qty_data = [];
        foreach ($request->input('products') as $product) {
            $products_qty_data[$product['id']] = $product['qty'];
        }

        $delivery_time = ($request->input('delivery_type') == 'fast') ? Carbon::tomorrow()->setHour(10) : Carbon::createFromFormat('Y-m-d H:i', $request->input('delivery_time'));

        $order_vendors = [];
        $order_products = [];

        $products_price = 0;
        $products_per_vendor = Product::whereIn('id', array_keys($products_qty_data))->get()->groupBy('vendor_id');
        foreach ($products_per_vendor as $vendor_id => $products) {
            $products_per_vendor_price = 0;

            foreach ($products as $product) {
                $qty = $products_qty_data[$product->id];
                $total = $product->price * $qty;
                $order_products[] = [
                    'product_id' => $product->id,
                    'vendor_id' => $vendor_id,
                    'unit_price' => $product->price,
                    'qty' => $qty,
                    'total' => $total
                ];

                $products_per_vendor_price += $total;
            }

            $order_vendors[] = [
                'vendor_id' => $vendor_id,
                'status' => 'new',
                'has_service' => $request->input('has_service', false),
                'delivery_time' =>  $delivery_time,
                'services_price' => 0,
                'tax_price' => 0,
                'delivery_price' => 0,
                'products_price' => $products_per_vendor_price,
                'total' => $products_per_vendor_price
            ];

            $products_price += $total;
        }

        $order_data = [
            'user_car_id' => $request->input('user_car_id'),
            'address_id' => $request->input('address_id'),
            'has_service' => $request->input('has_service', false),
            'delivery_time' =>  $delivery_time,
            'payment_method' => $request->input('payment_method'),
            'user_id' => $this->user->id,
            'status' => 'new',
            'type' =>  $type,
            'car_id' => UserCar::find($request->input('user_car_id'))->car_id,
            'products_price' => $products_price,
            'services_price' => 0,
            'tax_price' => 0,
            'delivery_price' => 0,
            'total' => $products_price
        ];


        /** @var Order $order */
        $order = Order::create($order_data);
        $order->vendors()->sync($order_vendors);
        $order->products()->sync($order_products);

        return new OrderResource($order);
    }

    public function listOrders(Request $request, string $type)
    {
        $orders = Order::with([
            'products',
            'products.thumbnail',
            'products.vendor',
            'user_car',
            'user_car.car',
            'user_car.car.model',
            'user_car.car.model.brand',
            'user_car.client',
        ])
            ->where('user_id', $this->user->id)
            ->where('type', $type)
            ->orderBy('id', 'desc');

        return $orders;
    }

    public function orderDetails(CarPartsOrderDetailsRequest $request, $type)
    {

        $with = [];

        switch ($type) {
            case 'winch':
                $with = [];
                break;

            default:
                $with = [
                    'products',
                    'products.thumbnail',
                    'products.vendor'
                ];
        }


        $order = Order::with($with)
            ->where('user_id', $this->user->id)
            ->where('id', $request->input('id'))
            ->first();

        switch ($type) {
            case 'winch':
                $res =  new WinchOrderResource($order);
                break;

            default:
                $res = new OrderResource($order);
        }

        return $res;
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        $order = Order::find($request->input('id'));
        $order->update([
            'status' => $request->input('status')
        ]);

        return new OrderResource($order);
    }

    public function getAvailableSlots(GetAvailableSoltsRequest $request)
    {
        $slots = [];

        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('date') . '10:00:00');
        $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('date') . '22:00:00');

        $i = 0;
        while ($endTime >= $startTime) {
            $slots[] = [
                "time" => $startTime->format('H:i:s'),
                "is_available" => (($i%2) == 0)
            ];
            $startTime->addHours(2);
            $i++;
        }

        return [
            'date' => $request->input('date'),
            'slots' => $slots
        ];
    }

    public function rateOrder(RateOrderRequest $request)
    {
        OrderRate::updateOrCreate(
            [
                'order_id' => $request->input('order_id')
            ],
            [
                'products' => $request->input('order_id'),
                'worker' => $request->input('worker_rate'),
                'services' => $request->input('services_rate'),
                'comment' => $request->input('comment'),
            ]
        );

        $order = Order::find($request->input('order_id'));

        return new OrderResource($order);
    }
}
