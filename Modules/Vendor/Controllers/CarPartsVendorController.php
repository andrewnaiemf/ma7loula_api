<?php

namespace Modules\Vendor\Controllers;

use Modules\Vendor\Resources\BT\Vendor\ProductResource;
use Modules\Core\Controllers\Controller;
use Modules\Core\Requests\Auth\LoginRequest;
use Modules\Vendor\Requests\BT\Vendor\ListOrdersRequest;
use Modules\Vendor\Requests\BT\Vendor\ListProductsRequest;
use Modules\Vendor\Requests\BT\Vendor\OrdersDetailsRequest;
use Modules\Vendor\Requests\BT\Vendor\ProductDetailsRequest;
use Modules\Vendor\Requests\BT\Vendor\RegisterRequest;
use Modules\Vendor\Requests\CarParts\AddProductRequest;
use Modules\Vendor\Requests\CarParts\UpdateOrderStatusRequest;
use Modules\Vendor\Resources\BT\Vendor\OrderResource;
use Modules\Vendor\Services\CarPartsVendorService;

class CarPartsVendorController extends Controller {
    public function __construct(private CarPartsVendorService $vendorService)
    {
        
    }

    public function appStart()
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

    public function registerRequirements(){
        return $this->successResponse([
            'requirements' => $this->vendorService->registerRequirements()
        ]);
    }

    public function register(RegisterRequest $request){
        return $this->successResponse([
            'user' => $this->vendorService->registerVendor($request)
        ]);
    }

    public function login(LoginRequest $request){
        return $this->successResponse([
            'user' => $this->vendorService->login($request, 'vendor_cp')
        ]);
    }

    public function userProfile()
    {
        return $this->successResponse([
            'user' => $this->vendorService->userProfile()
        ]);
    }

    public function listProducts(ListProductsRequest $request, $status){
        return $this->listResponse('products', $this->vendorService->listProducts($status), new ProductResource([]));
    }

    public function addProduct(AddProductRequest $request){
        return $this->successResponse([
            'product' => $this->vendorService->addProduct($request)
        ]);
    }

    public function productDetails(ProductDetailsRequest $request){
        return $this->successResponse([
            'product' => $this->vendorService->productDetails($request)
        ]);
    }

    public function listOrders(ListOrdersRequest $request, $status){
        return $this->listResponse(
            'orders',
            $this->vendorService->listOrders($request,  $status),
            new OrderResource([])
        );
    }

    public function orderDetails(OrdersDetailsRequest $request){
        return $this->successResponse([
            'order' => $this->vendorService->orderDetails($request)
        ]);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request){
        return $this->successResponse([
            'order' => $this->vendorService->updateOrderStatus($request)
        ]);
    }
}
