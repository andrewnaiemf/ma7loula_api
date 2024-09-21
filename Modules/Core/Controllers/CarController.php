<?php
namespace Modules\Core\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Requests\Car\ListCarsRequest;
use Modules\Core\Requests\Car\ListModelsRequest;
use Modules\Core\Services\CarService;

class CarController extends Controller{

    public function __construct(private CarService $carService)
    {
        
    }

    public function listBrands(){
        return $this->successResponse([
            'carBrands' => $this->carService->listBrands()
        ]);
    }

    public function listModels(ListModelsRequest $request){
        return $this->successResponse([
            'carModels' => $this->carService->listModels($request)
        ]);
    }

    public function listCars(ListCarsRequest $request){
        return $this->successResponse([
            'cars' => $this->carService->listCars($request)
        ]);
    }

    
}