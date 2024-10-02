<?php

namespace Modules\Client\Controllers;

use Modules\Client\Requests\Tire\ListBrandsRequest;
use Modules\Client\Requests\Tire\ListSizesRequest;
use Modules\Client\Requests\Tire\ListTiresRequest;
use Modules\Client\Requests\Tire\ListTypesRequest;
use Modules\Client\Resources\ProductResource;
use Modules\Client\Services\TireService;
use Modules\Core\Controllers\Controller;

class TireController extends Controller
{
    public function __construct(private TireService $tireService)
    {
        
    }

    public function listBrands(ListBrandsRequest $request){
        return $this->successResponse([
            'tireBrands' => $this->tireService->listBrands($request)
        ]);
    }

    public function listTypes(ListTypesRequest $request){
        return $this->successResponse([
            'tireTypes' => $this->tireService->listTypes($request)
        ]);
    }

    public function listSizes(ListSizesRequest $request){
        return $this->successResponse([
            'tireSizes' => $this->tireService->listSizes($request)
        ]);
    }

    public function listTires(ListTiresRequest $request){
        return $this->listResponse('tires', $this->tireService->listTires($request), new ProductResource([]));
    }

    public function productDetails(ListTiresRequest $request){
        return $this->listResponse('tires', $this->tireService->listTires($request), new ProductResource([]));
    }
}