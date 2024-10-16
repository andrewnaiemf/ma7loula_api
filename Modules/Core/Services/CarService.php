<?php

namespace Modules\Core\Services;

use App\Models\Car;
use App\Models\CarBrand;
use App\Models\CarModel;
use Modules\Core\Requests\Car\GetCarByIdRequest;
use Modules\Core\Requests\Car\ListCarsRequest;
use Modules\Core\Requests\Car\ListCarYearsRequest;
use Modules\Core\Requests\Car\ListModelsRequest;
use Modules\Core\Resources\CarBrandResource;
use Modules\Core\Resources\CarModelResource;
use Modules\Core\Resources\CarResource;

class CarService
{
    public function listBrands()
    {
        $car_brands_ids = Car::select('car_brand_id')->groupBy('car_brand_id')->get();
        $car_brands =  CarBrand::whereIn('id', $car_brands_ids)->get();
        return CarBrandResource::collection($car_brands);
    }

    public function listModels(ListModelsRequest $request)
    {
        $car_models_ids = Car::select('car_model_id')->groupBy('car_model_id')->where('car_brand_id', $request->input('car_brand_id'))->get();
        $car_models =  CarModel::with('brand')->whereIn('id', $car_models_ids)->get();
        return CarModelResource::collection($car_models);
    }

    public function listYears(ListCarYearsRequest $request)
    {
        $years = Car::select('year')
            ->where('car_brand_id', $request->input('car_brand_id'))
            ->where('car_model_id', $request->input('car_model_id'))
            ->orderBy('year', 'desc')
            ->groupBy('year')
            ->get()->pluck('year');

        return $years;
    }

    public function listCars(ListCarsRequest $request)
    {
        $cars = Car::with(['brand', 'brand.model'])
            ->where('car_brand_id', $request->input('car_brand_id'))
            ->where('car_model_id', $request->input('car_model_id'));

        if ($request->input('year')) {
            $cars->where('year', $request->input('year'));
        }

        $cars = $cars->orderBy('year', 'desc')->get();

        return CarResource::collection($cars);
    }

    public function getCarById(GetCarByIdRequest $request){
        $car = Car::with(['brand', 'brand.model'])->find($request->input('car_id'));
        return new CarResource($car);
    }
}
