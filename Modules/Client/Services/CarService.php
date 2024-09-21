<?php

namespace Modules\Client\Services;

use App\Models\ClientCar;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Requests\Car\AddCarRequest;
use Modules\Client\Resources\ClientCarResource;

class CarService
{
    private User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }
    public function addCar(AddCarRequest $request)
    {
        $client_car = ClientCar::create([
            'user_id' => $this->user->id,
            'car_id' => $request->input('car_id')
        ]);

        return new ClientCarResource($client_car);
    }
}
