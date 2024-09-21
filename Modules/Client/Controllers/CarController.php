<?php

namespace Modules\Client\Controllers;

use Illuminate\Http\Request;
use Modules\Client\Requests\Car\AddCarRequest;
use Modules\Client\Requests\Car\DeleteCarRequest;
use Modules\Client\Services\CarService;
use Modules\Core\Controllers\Controller;

class CarController extends Controller
{
    public function __construct(private CarService $carService)
    {
        
    }

    public function addCar(AddCarRequest $request){
        return $this->successResponse([
            'userCar' => $this->carService->addCar($request)
        ]);
    }

    public function listCars(){
        return $this->successResponse([
            'userCars' => $this->carService->listCars()
        ]);
    }

    public function deleteCar(DeleteCarRequest $request){
        return $this->successResponse([
            'success' => $this->carService->deleteCar($request)
        ]);
    }

    public function setDefault(DeleteCarRequest $request){
        return $this->successResponse([
            'userCar' => $this->carService->setDefault($request)
        ]);
    }
}