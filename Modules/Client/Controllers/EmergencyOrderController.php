<?php

namespace Modules\Client\Controllers;

use Illuminate\Http\Request;
use Modules\Client\Requests\CarParts\CarPartsOrderDetailsRequest;
use Modules\Client\Requests\EmergencyOrder\CreateEmergencyOrderRequest;
use Modules\Client\Requests\Order\UpdateOrderStatusRequest;
use Modules\Client\Requests\WinchOrder\AcceptWinchOffer;
use Modules\Client\Requests\WinchOrder\CalculateWinchOrderPriceRequest;
use Modules\Client\Requests\WinchOrder\CreateWinchOrderRequest;
use Modules\Client\Requests\WinchOrder\ListWinchDriverOffers;
use Modules\Client\Resources\WinchOrder\WinchOrderListResource;
use Modules\Client\Services\EmergencyOrderService;
use Modules\Core\Controllers\Controller;

class EmergencyOrderController extends Controller
{

    public function __construct(private EmergencyOrderService $orderService) {}

   /*  public function list(){
        return $this->listResponse(
            'orders',
            $this->orderService->listWinchOrders(),
            new WinchOrderListResource([])
        );
    } */

    public function createOrder(CreateEmergencyOrderRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->createEmergencyOrder($request)
        ]);
    }


   /*  public function orderDetails(CarPartsOrderDetailsRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->orderDetails($request, 'winch')
        ]);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        return $this->successResponse([
            'order' => $this->orderService->updateOrderStatus($request, 'winch')
        ]);
    }

    //for developing
    public function sendFakeOffer(Request $request)
    {
        $offers = $this->orderService->sendFakeOffer($request);
        return $this->successResponse();
    }

    public function listWinchDriversOffers(ListWinchDriverOffers $request)
    {
        return $this->successResponse([
            'wokers' => $this->orderService->listWinchDriversOffers($request)
        ]);
    }

    public function acceptOffer(AcceptWinchOffer $request){
        return $this->successResponse([
            'order' => $this->orderService->acceptOffer($request)
        ]);
    } */
}
