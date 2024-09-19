<?php

namespace Modules\Client\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Modules\Client\Requests\Address\CreateAddressRequest;
use Modules\Client\Requests\Address\DeleteAddressRequest;
use Modules\Client\Requests\Address\UpdateAddressRequest;
use Modules\Client\Services\AddressService;
use Modules\Core\Controllers\Controller;

class AddressController extends Controller
{
    public function __construct(private AddressService $addressService)
    {
        
    }

    public function create(CreateAddressRequest $request){
        return $this->successResponse([
            'address' => $this->addressService->create($request)
        ]);
    }

    public function list(Request $request){
        return $this->successResponse([
            'addresses' => $this->addressService->list($request)
        ]);
    }

    public function update(UpdateAddressRequest $request){
        return $this->successResponse([
            'address' => $this->addressService->update($request)
        ]);
    }

    public function delete(DeleteAddressRequest $request){
        return $this->successResponse([
            'success' => $this->addressService->delete($request)
        ]);
    }

    public function setDefault(DeleteAddressRequest $request){
        return $this->successResponse([
            'success' => $this->addressService->setDefault($request)
        ]);
    }
}