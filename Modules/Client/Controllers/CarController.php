<?php

namespace Modules\Client\Controllers;

use Illuminate\Http\Request;
use Modules\Client\Requests\Car\AddCarRequest;
use Modules\Client\Services\CarService;
use Modules\Core\Controllers\Controller;

class CarController extends Controller
{
    public function __construct(private CarService $carService)
    {
        
    }

    public function addCar(AddCarRequest $request){
        return $this->successResponse([
            'clientCar' => $this->carService->addCar($request)
        ]);
    }
}