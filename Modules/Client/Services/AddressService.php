<?php

namespace Modules\Client\Services;

use App\Models\Address;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Requests\Address\CreateAddressRequest;
use Modules\Client\Requests\Address\DeleteAddressRequest;
use Modules\Client\Requests\Address\UpdateAddressRequest;
use Modules\Client\Resources\AddressResource;

class AddressService
{

    private User $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    public function create(CreateAddressRequest $request, Address $address = null): AddressResource
    {
        $data = $request->all([
            'name',
            "city_id",
            "state_id",
            "lat",
            "lon",
            "details"
        ]);

        $data = array_merge($data, [
            'user_id' => $this->user->id,
            'client_id' => $this->user->client->id
        ]);

        if (!$address) {
            $address = Address::create($data);
        }

        if ($request->input("is_default") || $this->user->defaultAddress == null) {
            $this->user->client->update([
                'default_address_id' => $address->id
            ]);
        }

        return new AddressResource($address);
    }

    public function list(Request $request)
    {
        $addresses = Address::with(['city', 'state', 'client'])->where('user_id', $this->user->id)->orderBy('id', 'desc')->get()->sortByDesc('is_default');
        return AddressResource::collection($addresses);
    }

    public function update(UpdateAddressRequest $request)
    {
        $address = Address::find($request->input('id'));
        return $this->create($request, $address);
    }

    public function delete(DeleteAddressRequest $request): bool
    {
        return Address::where('id', $request->input('id'))->delete();
    }

    public function setDefault(DeleteAddressRequest $request) {
        return $this->user->client->update([
            'default_address_id' => $request->input('id')
        ]);
    }
}
