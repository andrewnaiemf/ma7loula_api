<?php
namespace Modules\Core\Controllers;

use App\Models\City;
use App\Models\State;
use Modules\Core\Requests\Address\ListCitiesRequest;

class AddressController extends Controller{
    public function states(){
        return $this->successResponse(["states" => State::all()]);
    }

    public function cities(ListCitiesRequest $request){
        return $this->successResponse([
            "cities" => City::where('state_id', $request->input('state_id'))->get()
        ]);
    }
}