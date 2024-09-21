<?php

namespace Modules\Client\Services;

use App\Models\User;
use App\Models\UserCar;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Requests\Car\AddCarRequest;
use Modules\Client\Requests\Car\DeleteCarRequest;
use Modules\Client\Resources\UserCarResource;

class CarService
{
    private User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function addCar(AddCarRequest $request)
    {
        $client_car = UserCar::updateOrCreate([
            'user_id' => $this->user->id,
            'client_id' => $this->user->client->id,
            'car_id' => $request->input('car_id')
        ]);

        if ($request->input('is_default') || $this->user->defaultCar == null) {
            $this->user->client->update([
                'default_car_id' => $request->input('car_id')
            ]);
        }

        return new UserCarResource($client_car);
    }

    public function listCars()
    {
        $client_cars = UserCar::with(['car', 'car.model.brand', 'client'])->where('user_id', $this->user->id)->get()->append('is_default')->sortByDesc('is_default');
        return UserCarResource::collection($client_cars);
    }

    public function deleteCar(DeleteCarRequest $request)
    {
        return UserCar::where('id', $request->input('user_car_id'))->delete();
    }

    public function setDefault(DeleteCarRequest $request)
    {
        $userCar = UserCar::find($request->input('user_car_id'));
        
        $this->user->client->update([
            'default_car_id' => $userCar->car_id
        ]);

        return new UserCarResource($userCar);
    }
}
