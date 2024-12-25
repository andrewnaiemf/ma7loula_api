<?php

namespace Modules\Emergency\Controllers;

use Modules\Client\Resources\EmergencyOrder\EmergencyOrderResource;
use Modules\Core\Controllers\Controller;
use Modules\Core\Requests\Auth\LoginRequest;
use Modules\Emergency\Requests\RegisterRequest;
use Modules\Emergency\Services\EmergencyService;
use Modules\Emergency\Requests\ListOrdersRequest;
use Modules\Emergency\Requests\OrdersDetailsRequest;
use Modules\Emergency\Requests\SendOfferRequest;
use Modules\Emergency\Requests\UpdateLocationRequest;
use Modules\Emergency\Requests\UpdateOrderServicesRequest;
use Modules\Emergency\Requests\UpdateOrderStatusRequest;

class EmergencyController extends Controller {


    public function __construct(private EmergencyService $emergencyService)
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
            'requirements' => $this->emergencyService->registerRequirements()
        ]);
    }

    public function register(RegisterRequest $request){
        return $this->successResponse([
            'user' => $this->emergencyService->register($request)
        ]);
    }

    public function login(LoginRequest $request){
        return $this->successResponse([
            'user' => $this->emergencyService->login($request, 'worker_sos')
        ]);
    }

    public function userProfile()
    {
        return $this->successResponse([
            'user' => $this->emergencyService->userProfile()
        ]);
    }

    public function listOrders(ListOrdersRequest $request){
        return $this->listResponse(
            'orders',
            $this->emergencyService->listOrders($request),
            new EmergencyOrderResource([])
        );
    }
    
    public function orderDetails(OrdersDetailsRequest $request){
        return $this->successResponse([
            'order' => $this->emergencyService->orderDetails($request)
        ]);
    }

    public function checkForOrders(){
        return $this->successResponse($this->emergencyService->checkForOrders());
    }

    public function sendOffer(SendOfferRequest $request){
        return $this->successResponse([
            'success' => $this->emergencyService->sendOffer($request)
        ]);
    }

    public function rejectRequest(SendOfferRequest $request){
        return $this->successResponse([
            'success' => $this->emergencyService->rejectRequest($request)
        ]);
    }

    public function updateLocation(UpdateLocationRequest $request){
        return $this->successResponse([
            'success' => $this->emergencyService->updateLocation($request)
        ]);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        return $this->successResponse([
            'order' => $this->emergencyService->updateOrderStatus($request, 'winch')
        ]);
    }

    public function listServices(){
        return $this->successResponse([
            'services' => 
            $this->emergencyService->listServices()
        ]);
    }

    public function updateOrderServices(UpdateOrderServicesRequest $request){
        return $this->successResponse([
            'services' => 
            $this->emergencyService->updateOrderServices($request)
        ]);
    }
}
