<?php

namespace Modules\Client\Controllers;

use App\Models\Order;
use App\Models\Worker;
use Modules\Client\Events\WinchOrder\WorkerAcceptedOrder;
use Modules\Client\Requests\WinchOrder\CalculateWinchOrderPriceRequest;
use Modules\Client\Requests\WinchOrder\CreateWinchOrderRequest;
use Modules\Client\Services\WinchOrderService;
use Modules\Core\Controllers\Controller;

class WinchOrderController extends Controller
{

    public function __construct(private WinchOrderService $orderService) {}


    public function calculatePrice(CalculateWinchOrderPriceRequest $request)
    {
        return $this->successResponse([
            'price' => $this->orderService->calculatePrice($request)
        ]);
    }


    public function createOrder(CreateWinchOrderRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->createWinchOrder($request)
        ]);
    }


    public function orderDetails(CreateWinchOrderRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->createWinchOrder($request)
        ]);
    }

    public function testEmitEvent()
    {
        $order = Order::find(26);
        $user = $order->user;
        $worker = Worker::find(10);

        broadcast(new WorkerAcceptedOrder($user, $order, $worker));
        
    }
}
