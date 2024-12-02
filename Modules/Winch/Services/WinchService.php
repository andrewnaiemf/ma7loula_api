<?php

namespace Modules\Winch\Services;

use App\Models\Order;
use App\Models\OrderVendor;
use App\Models\OrderWinch;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Client\Resources\WinchOrder\WinchOrderOfferResource;
use Modules\Client\Resources\WinchOrder\WinchOrderResource;
use Modules\Core\Services\AuthService;
use Modules\Vendor\Resources\BT\Vendor\OrderResource;
use Modules\Winch\Requests\ListOrdersRequest;
use Modules\Winch\Requests\OrdersDetailsRequest;
use Modules\Winch\Requests\SendOfferRequest;
use Modules\Winch\Requests\UpdateLocationRequest;
use Modules\Winch\Requests\UpdateOrderStatusRequest;
use Modules\Winch\Resources\WinchUserResource;

class WinchService extends AuthService
{
    public function __construct(private AuthService $authService) {}

    public function registerRequirements()
    {
        return [
            [
                'title' => 'مسح رقم الهوية',
                'body' => 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي'
            ],
            [
                'title' => 'رخصة الشركة',
                'body' => 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي'
            ],
            [
                'title' => 'الرقم الضريبي',
                'body' => 'هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي'
            ]
        ];
    }

    public function UserResource($user): JsonResource
    {
        return new WinchUserResource($user);
    }

    public function register(Request $request): JsonResource
    {
        $data = $request->all(['name', 'email', 'phone', 'password']);
        $data['role_id'] = 5;

        //create user
        $user =  User::create($data);
        $user->auth_token = $user->createToken('auth', ['*'], Carbon::now()->addDays(120))->plainTextToken;

        $attributes = [];

        //handle media
        $files_keys = ['id_image', 'criminal_record_image', 'car_licence_image', 'driver_licence_image'];
        if (!is_dir(storage_path('app/public/winch-driver/'))) {
            mkdir(storage_path('app/public/winch-driver/'));
        }
        foreach ($files_keys as $key) {
            if ($request->input($key)) {
                $filename = $request->input($key);
                $path = storage_path('app/public/temp/' . $filename);
                $new_path = storage_path('app/public/winch-driver/' . $filename);
                if (file_exists($path)) {
                    rename($path, $new_path);
                }
                $attributes[] = [
                    'key' => $key,
                    'value' => 'storage/winch-driver/' . $filename
                ];
            }
        }

        //create vendor
        $worker = Worker::create([
            'user_id' => $user->id,
            'vendor_id' => $request->input('vendor_id'),
            'car_plate_number' => $request->input('car_plate_no'),
            'type' => 'winch',
        ]);

        $attributes_keys = $request->all(['driver_licence_no', 'driver_licence_expire_date', 'car_licence_no', 'car_licence_expire_date']);

        foreach ($attributes_keys as $key) {
            if ($request->has($key)) {
                $attributes[] = [
                    'key' => $key,
                    'value' => $request->input($key)
                ];
            }
        }

        foreach ($attributes as $attr) {
            WorkerAttribute::create([
                'worker_id' => $worker->id,
                'key' => $attr['key'],
                'value' => $attr['value']
            ]);
        }

        return new WinchUserResource($user);
    }

    public function listOrders(ListOrdersRequest $request)
    {
        $worker_id = Auth::user()->worker->id;

        $orders = Order::whereHas('winch_order', function ($q) use ($worker_id) {
            $q
                ->where('worker_id', $worker_id)
                ->where('status', 'completed');
        })
            ->orderBy('id', 'desc')
            ->where('status', 'completed');

        return $orders;
    }

    public function orderDetails(OrdersDetailsRequest $request)
    {
        $order = Order::find($request->input('id'));

        return new WinchOrderResource($order);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        $orderVendor = OrderVendor::where('order_id', $request->input('id'))->first();
        $orderVendor->update([
            'status' => $request->input('status')
        ]);

        $order = $orderVendor->order;

        return new WinchOrderResource($order);
    }

    public function updateLocation(UpdateLocationRequest $request)
    {
        $user = Auth::user();

        Worker::where('user_id', $user->id)->update([
            'lat' => $request->input('lat'),
            'lon' => $request->input('lon')
        ]);

        return true;
    }

    public function rejectRequest(SendOfferRequest $request){
        $worker = Auth::user()->worker;
        $order_id = $request->input('order_id');
        $order_key = 'winch_request_' . $worker->id . '_' . $order_id;
        Cache::forget($order_key);
        return true;
    }

    public function sendOffer(SendOfferRequest $request)
    {
        $user = Auth::user();
        $worker = Worker::where('user_id', $user->id)->first();

        $order_offers_keys = 'winch_order_offers_' . $request->input('order_id');
        $worker_offer_key = 'winch_order_offers_' . $request->input('order_id') . '_' . $worker->id;


        if (!Cache::has($worker_offer_key)) {

            $new_offer = (new WinchOrderOfferResource($worker));

            if (Cache::has($order_offers_keys)) {
                $offers = Cache::get($order_offers_keys);
            } else {
                $offers = [];
            }

            array_unshift($offers, $worker_offer_key);

            Cache::put($order_offers_keys, $offers, 60);
            Cache::put($worker_offer_key, $new_offer, 60);

            Cache::forget('winch_request_' . $worker->id . '_' .  $request->input('order_id'));

            return true;
        }

        return false;
    }

    public function checkForOrders()
    {
        $user = Auth::user();
        $worker = $user->worker;


        $accepted_offer = null;
        $accepted_offer_key = 'accepted_winch_request_' . $worker->id;
        if (Cache::has($accepted_offer_key)) {
            $accepted_offer = Cache::get($accepted_offer_key);
        }

        $requests = [];
        if ($accepted_offer == null) {
            $worker_key = 'winch_request_' . $worker->id;
            if (Cache::has($worker_key)) {
                $request_keys = Cache::get($worker_key);
                foreach ($request_keys as $request_key) {
                    if (Cache::has($request_key)) {
                        $requests[] = Cache::get($request_key);
                    }
                }
            }
        }

        return [
            'accepted_offer' => $accepted_offer,
            'requests' => $requests,
        ];
    }
}
