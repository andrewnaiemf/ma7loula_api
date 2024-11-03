<?php
namespace Modules\Core\Controllers;

use Illuminate\Http\Request;
use Modules\Core\Requests\Car\GetCarByIdRequest;
use Modules\Core\Requests\Car\ListCarsRequest;
use Modules\Core\Requests\Car\ListCarYearsRequest;
use Modules\Core\Requests\Car\ListModelsRequest;
use Modules\Core\Resources\CarResource;
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

    public function listYears(ListCarYearsRequest $request){
        return $this->successResponse([
            'years' => $this->carService->listYears($request)
        ]);
    }

    public function listCars(ListCarsRequest $request){
        return $this->successResponse([
            'cars' => $this->carService->listCars($request)
        ]);
    }

    public function getCarById(GetCarByIdRequest $request){
        return $this->successResponse([
            'car' => $this->carService->getCarById($request)
        ]);
    }

    public function listAllCars(){
        return $this->listResponse('cars', $this->carService->listAllCars(), new CarResource([]));
    }
    
}