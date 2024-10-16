<?php

namespace Modules\Client\Controllers;

use App\Models\Order;
use App\Models\Worker;
use Illuminate\Http\Request;
use Modules\Client\Events\WinchOrder\WorkerAcceptedOrder;
use Modules\Client\Requests\WinchOrder\CalculateWinchOrderPriceRequest;
use Modules\Client\Requests\WinchOrder\CreateWinchOrderRequest;
use Modules\Client\Requests\WinchOrder\ListWinchDriverOffers;
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

    //for developing
    public function sendFakeOffer(Request $request)
    {
        return $this->successResponse(/* [
            'offers' => $this->orderService->sendFakeOffer($request)
        ] */);
    }

    public function listWinchDriversOffers(ListWinchDriverOffers $request)
    {
        return $this->successResponse([
            'wokers' => $this->orderService->listWinchDriversOffers($request)
        ]);
    }
}
