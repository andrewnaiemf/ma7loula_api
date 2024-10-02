<?php

namespace Modules\Client\Controllers;

use Modules\Client\Requests\Battery\ListBatteriesRequest;
use Modules\Client\Requests\Battery\ListBrandsRequest;
use Modules\Client\Requests\Battery\ListVoltRequest;
use Modules\Client\Resources\ProductResource;
use Modules\Client\Services\BatteryService;
use Modules\Core\Controllers\Controller;

class BatteryController extends Controller
{
    public function __construct(private BatteryService $batteryService){}

    public function listVolts(ListVoltRequest $request){
        return $this->successResponse([
            'batteryVolts' => $this->batteryService->listVoles($request)
        ]);
    }

    public function listBrands(ListBrandsRequest $request){
        return $this->successResponse([
            'batteryBrands' => $this->batteryService->listBrands($request)
        ]);
    }

    public function listBatteries(ListBatteriesRequest $request){
        return $this->listResponse('batteries', $this->batteryService->listBatteries($request), new ProductResource([]));
    }
}