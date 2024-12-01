<?php

namespace Modules\Winch\Controllers;

use Modules\Client\Resources\WinchOrder\WinchOrderListResource;
use Modules\Core\Controllers\Controller;
use Modules\Core\Requests\Auth\LoginRequest;
use Modules\Core\Requests\Request;
use Modules\Winch\Requests\ListOrdersRequest;
use Modules\Winch\Requests\OrdersDetailsRequest;
use Modules\Winch\Requests\RegisterRequest;
use Modules\Winch\Requests\SendOfferRequest;
use Modules\Winch\Requests\UpdateLocationRequest;
use Modules\Winch\Requests\UpdateOrderStatusRequest;
use Modules\Winch\Services\WinchService;

class WinchController extends Controller {


    public function __construct(private WinchService $winchService)
    {
        
    }

    public function homeSliders()
    {
        return $this->successResponse([
            'sliders' => [
                [
                    'image' => url('assets/temp/sliders/1.jpg'),
                    'clickable' => 'link',
                    'link' => 'http://google.com',
                    'product_id' => null
                ],
                [
                    'image' => url('assets/temp/sliders/2.jpg'),
                    'clickable' => 'product',
                    'link' => null,
                    'product_id' => 1
                ],
                [
                    'image' => url('assets/temp/sliders/3.jpg'),
                    'clickable' => null,
                    'link' => null,
                    'product_id' => null
                ]
            ]
        ]);
    }

    public function AppStart()
    {
        return $this->successResponse([
            'sliders' => [
                [
                    'image' => url('assets/temp/start/1.jpg'),
                    'text' => trans('Core::messages.start.0.text'),
                    'subtext' => trans('Core::messages.start.0.subtext'),
                ],
                [
                    'image' => url('assets/temp/start/2.jpg'),
                    'text' => trans('Core::messages.start.1.text'),
                    'subtext' => trans('Core::messages.start.1.subtext'),
                ]
            ]
        ]);
    }

    public function registerRequirements(){
        return $this->successResponse([
            'requirements' => $this->winchService->registerRequirements()
        ]);
    }

    public function register(RegisterRequest $request){
        return $this->successResponse([
            'user' => $this->winchService->register($request)
        ]);
    }

    public function login(LoginRequest $request){
        return $this->successResponse([
            'user' => $this->winchService->login($request, 'winch_driver')
        ]);
    }

    public function userProfile()
    {
        return $this->successResponse([
            'user' => $this->winchService->userProfile()
        ]);
    }

    public function listOrders(ListOrdersRequest $request){
        return $this->listResponse(
            'orders',
            $this->winchService->listOrders($request),
            new WinchOrderListResource([])
        );
    }
    
    public function orderDetails(OrdersDetailsRequest $request){
        return $this->successResponse([
            'order' => $this->winchService->orderDetails($request)
        ]);
    }

    public function checkForOrders(){
        return $this->successResponse($this->winchService->checkForOrders());
    }

    public function sendOffer(SendOfferRequest $request){
        return $this->successResponse([
            'success' => $this->winchService->sendOffer($request)
        ]);
    }

    public function rejectRequest(SendOfferRequest $request){
        return $this->successResponse([
            'success' => $this->winchService->rejectRequest($request)
        ]);
    }

    public function updateLocation(UpdateLocationRequest $request){
        return $this->successResponse([
            'success' => $this->winchService->updateLocation($request)
        ]);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        return $this->successResponse([
            'order' => $this->winchService->updateOrderStatus($request, 'winch')
        ]);
    }
}
