<?php

namespace Modules\Client\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Modules\Client\Requests\CarParts\CarPartsOrderDetailsRequest;
use Modules\Client\Requests\CarParts\CreateOrderRequest as CreateCarPartsOrderRequest;
use Modules\Client\Requests\Order\GetAvailableSoltsRequest;
use Modules\Client\Requests\Order\RateOrderRequest;
use Modules\Client\Requests\Order\UpdateOrderStatusRequest;
use Modules\Client\Resources\OrderListCarPartsResource;
use Modules\Client\Services\OrderService;
use Modules\Core\Controllers\Controller;

class OrderController extends Controller
{

    public function __construct(private OrderService $orderService) {}

    public function orderDetails(CarPartsOrderDetailsRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->orderDetails($request, 'car-parts')
        ]);
    }


    #car parts orders
    public function createCarPartsOrder(CreateCarPartsOrderRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->createOrder($request, 'car-parts')
        ]);
    }

    public function listCarPartsOrder(Request $request)
    {
        return $this->listResponse(
            'orders',
            $this->orderService->listOrders($request, 'car-parts'),
            new OrderListCarPartsResource([])
        );
    }



    #tire orders
    public function createTiresOrder(CreateCarPartsOrderRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->createOrder($request, 'tire')
        ]);
    }

    public function listTiresOrder(Request $request)
    {
        return $this->listResponse(
            'orders',
            $this->orderService->listOrders($request, 'tire'),
            new OrderListCarPartsResource([])
        );
    }


    #battery orders
    public function createBatteryOrder(CreateCarPartsOrderRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->createOrder($request, 'battery')
        ]);
    }

    public function lisBatteryOrder(Request $request)
    {
        return $this->listResponse(
            'orders',
            $this->orderService->listOrders($request, 'battery'),
            new OrderListCarPartsResource([])
        );
    }


    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->updateOrderStatus($request)
        ]);
    }

    public function rateOrder(RateOrderRequest $request) {
        return $this->successResponse([
            'order' => $this->orderService->rateOrder($request)
        ]);
    }

    public function getAvailableSlots(GetAvailableSoltsRequest $request)
    {
        return $this->successResponse($this->orderService->getAvailableSlots($request));
    }
}
