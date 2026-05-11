<?php

namespace Modules\Client\Services;

use App\Enums\OrderStatus;
use App\Enums\OrderVendorLineStatus;
use App\Jobs\SendVendorOfferDecisionPushJob;
use App\Jobs\SendVendorNewOrderPushJob;
use App\Models\Order;
use App\Models\OrderRate;
use App\Models\OrderVendor;
use App\Models\Product;
use App\Models\User;
use App\Models\UserCar;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Requests\CarParts\CarPartsOrderDetailsRequest;
use Modules\Client\Requests\Order\CreateOrderRequest;
use Modules\Client\Requests\Order\GetAvailableSoltsRequest;
use Modules\Client\Requests\Order\RateOrderRequest;
use Modules\Client\Requests\Order\RespondVendorOfferRequest;
use Modules\Client\Requests\Order\UpdateOrderStatusRequest;
use Modules\Client\Resources\EmergencyOrder\EmergencyOrderResource;
use Modules\Client\Resources\OrderCarPartsResource;
use Modules\Client\Resources\OrderResource;
use Modules\Client\Resources\WinchOrder\WinchOrderResource;
use Modules\Core\Exceptions\HttpErrorException;

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
        //$time_slot = ($request->input('delivery_type') == 'fast') ? NULL : date('H:i:s', strtotime($request->input('delivery_time')));
     
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
                'status' => OrderVendorLineStatus::New->value,
                'has_service' => $request->input('has_service', false),
                'delivery_time' =>  $delivery_time,
                
                'services_price' => 0,
                'tax_price' => 0,
                'delivery_price' => 0,
                'products_price' => $products_per_vendor_price,
                'total' => $products_per_vendor_price
            ];

            $products_price += $products_per_vendor_price;
        }
       // dd($time_slot);
        $tax = \App\Models\Setting::first()?->tax_percentage??10;
        $total_with_tax = $products_price + ($products_price * $tax / 100);
        $order_data = [
            'user_car_id' => $request->input('user_car_id'),
            'address_id' => $request->input('address_id'),
            'has_service' => $request->input('has_service', false),
            'delivery_time' =>  $delivery_time,
           // 'time_slot'=>$time_slot,
            'payment_method' => $request->input('payment_method'),
            'user_id' => $this->user->id,
            'status' => OrderStatus::New->value,
            'type' =>  $type,
            'car_id' => UserCar::find($request->input('user_car_id'))->car_id,
            'products_price' => $products_price,
            'services_price' => 0,
            //'tax_price' => 0,
            'tax_price' => $tax,
            'delivery_price' => 0,
           // 'total' => $products_price
           'total' => $total_with_tax
        ];


        /** @var Order $order */
        $order = Order::create($order_data);
        $syncByVendor = [];
        foreach ($order_vendors as $row) {
            $vendorId = $row['vendor_id'];
            $syncByVendor[$vendorId] = Arr::except($row, ['vendor_id']);
        }
        $order->vendors()->sync($syncByVendor);
        $order->products()->sync($order_products);

        $vendorIds = collect($order_vendors)->pluck('vendor_id')->unique()->values()->all();
        SendVendorNewOrderPushJob::dispatch($vendorIds, $order->id, $type);

        return new OrderResource($order);
    }

    public static function syncAggregatesFromVendorLines(Order $order): void
    {
        $lines = $order->vendor_orders()->get();
        if ($lines->isEmpty()) {
            return;
        }

        $productsSum = $lines->sum(fn ($l) => (float) $l->products_price);
        $orderSum = $lines->sum(fn ($l) => (float) $l->total);

        $order->forceFill([
            'products_price' => (string) $productsSum,
            'total' => (string) $orderSum,
        ])->saveQuietly();
    }

    public function respondToVendorOffer(RespondVendorOfferRequest $request, string $type)
    {
        $ov = OrderVendor::query()
            ->whereKey($request->input('order_vendor_id'))
            ->whereHas('order', fn ($q) => $q->where('user_id', $this->user->id)->where('type', $type)->whereNull('deleted_at'))
            ->first();

        if (! $ov) {
            throw new HttpErrorException(__('Order vendor line not found for this customer/order type.'), [], 404);
        }

        if ($ov->status !== OrderVendorLineStatus::OfferPending->value) {
            throw new HttpErrorException(__('No pending offer for this line.'), [], 422);
        }

        if ($request->input('action') === 'accept') {
            if ($ov->offered_total === null || $ov->offered_total === '') {
                throw new HttpErrorException(__('Offer amount missing.'), [], 422);
            }
            $offered = (string) $ov->offered_total;
            $ov->update([
                'status' => OrderVendorLineStatus::Confirmed->value,
                'products_price' => $offered,
                'total' => $offered,
                'offered_total' => null,
            ]);
        } else {
            $ov->update([
                'status' => OrderVendorLineStatus::OfferDeclined->value,
                'offered_total' => null,
            ]);
        }

        SendVendorOfferDecisionPushJob::dispatch(
            (int) $ov->id,
            (string) $request->input('action')
        );

        $order = $ov->order()->first();
        if ($order) {
            self::syncAggregatesFromVendorLines($order);
        }

        $order = Order::with([
            'products',
            'products.thumbnail',
            'products.vendor',
            'vendor_orders',
        ])
            ->where('user_id', $this->user->id)
            ->where('type', $type)
            ->find($ov->order_id);

        return $this->returnResource($order, $type);
    }

    public function listOrders(Request $request, string $type)
    {
        $orders = Order::with([
            'products',
            'products.thumbnail',
            'products.vendor',
            'vendor_orders',
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

            case 'emergency':
                $with = [];
                break;

            default:
                $with = [
                    'products',
                    'products.thumbnail',
                    'products.vendor',
                    'vendor_orders',
                ];
        }


        $order = Order::with($with)
            ->where('user_id', $this->user->id)
            ->where('id', $request->input('id'))
            ->first();

        

        return $this->returnResource($order, $type);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request, string $type = 'car-parts')
    {
        $order = Order::find($request->input('id'));
        $order->update([
            'status' => $request->input('status'),
            'reason' => $request->input('reason')??NULL
        ]);

        return $this->returnResource($order, $type);
    }

    public function returnResource($order, $type){
        $res = new OrderResource($order);

        switch ($type) {
            case 'winch':
                $res =  new WinchOrderResource($order);
                break;

            case 'emergency':
                $res =  new EmergencyOrderResource($order);
                break;

            default:
                $res = new OrderResource($order);
        }

        return $res;
    }

    public function getAvailableSlots(GetAvailableSoltsRequest $request)
    {
        $slots = [];

        $startTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('date') . '10:00:00');
        $endTime = Carbon::createFromFormat('Y-m-d H:i:s', $request->input('date') . '22:00:00');

        $i = 0;
        while ($endTime >= $startTime) {
        //     $date = $request->input('date') . ' ' . $startTime->format('H:i:s');
        //     $order = Order::where('delivery_time', $date)
        //    ->where('type', $request->input('type'))
        //    ->where('status','!=','cancelled')
        //     ->first();

            $slots[] = [
                "time" => $startTime->format('h:i:s a'),
                "is_available" => ($i % 2 == 0)
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

    /**
     * Pivot attributes for order_vendors rows on service orders (winch / emergency).
     */
    protected function serviceOrderVendorPivotFromOrder(Order $order, ?int $workerId, ?string $lineStatus = null): array
    {
        $dt = $order->delivery_time;
        if ($dt instanceof Carbon) {
            $deliveryStr = $dt->format('Y-m-d H:i:s');
        } elseif (is_string($dt) && $dt !== '') {
            $deliveryStr = $dt;
        } else {
            $deliveryStr = now()->format('Y-m-d H:i:s');
        }

        return [
            'worker_id' => $workerId,
            'status' => $lineStatus ?? OrderVendorLineStatus::New->value,
            'has_service' => (bool) ($order->has_service ?? false),
            'delivery_time' => $deliveryStr,
            'products_price' => (string) $order->products_price,
            'services_price' => (string) $order->services_price,
            'tax_price' => (string) $order->tax_price,
            'delivery_price' => (string) $order->delivery_price,
            'total' => (string) $order->total,
        ];
    }

    /**
     * Ensure each notified vendor has an order_vendors row so vendor apps can open details by order_vendor id.
     *
     * @param  list<int>  $vendorIds
     */
    protected function ensureServiceOrderVendorStubs(Order $order, array $vendorIds): void
    {
        foreach ($vendorIds as $vendorId) {
            OrderVendor::firstOrCreate(
                [
                    'order_id' => $order->id,
                    'vendor_id' => (int) $vendorId,
                ],
                $this->serviceOrderVendorPivotFromOrder($order, null, OrderVendorLineStatus::New->value)
            );
        }
    }

    /**
     * When a worker/vendor accepts a service order, attach worker to the line and cancel other pending stubs.
     */
    protected function recordServiceOrderAcceptedVendor(Order $order, int $vendorId, int $workerId): void
    {
        OrderVendor::updateOrCreate(
            [
                'order_id' => $order->id,
                'vendor_id' => $vendorId,
            ],
            $this->serviceOrderVendorPivotFromOrder($order, $workerId, (string) $order->status)
        );

        OrderVendor::query()
            ->where('order_id', $order->id)
            ->where('vendor_id', '!=', $vendorId)
            ->whereNull('worker_id')
            ->update(['status' => OrderVendorLineStatus::Cancelled->value]);
    }
}
