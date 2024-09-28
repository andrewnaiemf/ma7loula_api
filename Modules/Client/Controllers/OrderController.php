<?php

namespace Modules\Client\Controllers;

use Illuminate\Http\Request;
use Modules\Client\Requests\CarParts\CarPartsOrderDetailsRequest;
use Modules\Client\Requests\CarParts\CreateOrderRequest as CreateCarPartsOrderRequest;
use Modules\Client\Resources\OrderCarPartsResource;
use Modules\Client\Resources\OrderListCarPartsResource;
use Modules\Client\Services\OrderService;
use Modules\Core\Controllers\Controller;

class OrderController extends Controller
{

    public function __construct(private OrderService $orderService) {}

    public function createCarPartsOrder(CreateCarPartsOrderRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->createCarPartsOrder($request)
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

    public function orderDetails(CarPartsOrderDetailsRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->orderDetails($request, 'car-parts')
        ]);
    }
}
