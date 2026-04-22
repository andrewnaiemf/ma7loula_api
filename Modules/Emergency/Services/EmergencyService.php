<?php

namespace Modules\Emergency\Services;

use App\Models\Order;
use App\Models\OrderVendor;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkerAttribute;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Client\Resources\EmergencyOrder\EmergencyOrderResource;
use Modules\Client\Resources\WinchOrder\WinchOrderOfferResource;
use Modules\Core\Services\AuthService;
use Modules\Emergency\Requests\ListOrdersRequest;
use Modules\Emergency\Requests\OrdersDetailsRequest;
use Modules\Emergency\Requests\SendOfferRequest;
use Modules\Emergency\Requests\UpdateLocationRequest;
use Modules\Emergency\Requests\UpdateOrderServicesRequest;
use Modules\Emergency\Requests\UpdateOrderStatusRequest;
use Modules\Emergency\Resources\WinchUserResource;

class EmergencyService extends AuthService
{
    public function __construct(private AuthService $authService) {}

    public function registerRequirements()
    {
        if(request()->header('lang') == 'ar') {
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
        }else{
            return [
                [
                    'title' => 'ID Number Scan',
                    'body' => 'It is a long-established fact that a reader will be distracted by the readable content of a page when looking at its layout.'
                ],
                [
                    'title' => 'Company License',
                    'body' => 'It is a long-established fact that a reader will be distracted by the readable content of a page when looking at its layout.'
                ],
                [
                    'title' => 'Tax Number',
                    'body' => 'It is a long-established fact that a reader will be distracted by the readable content of a page when looking at its layout.'
                ]
            ];

        }
    }

    public function UserResource($user): JsonResource
    {
        return new WinchUserResource($user);
    }

    public function register(Request $request): JsonResource
    {
        $data = $request->all(['name', 'email', 'phone', 'password']);
        $data['role_id'] = 7;

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
            'type' => 'emergency',
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

        $orders = Order::whereHas('emergency_order', function ($q) use ($worker_id) {
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

        return new EmergencyOrderResource($order);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request)
    {
        $orderVendor = OrderVendor::where('order_id', $request->input('id'))->first();
        $orderVendor->update([
            'status' => $request->input('status')
        ]);

        $order = $orderVendor->order;

        return new EmergencyOrderResource($order);
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

    public function rejectRequest(SendOfferRequest $request)
    {
        $worker = Auth::user()->worker;
        $order_id = $request->input('order_id');
        $order_key = 'emergency_request_' . $worker->id . '_' . $order_id;
        Cache::forget($order_key);
        return true;
    }

    public function sendOffer(SendOfferRequest $request)
    {
        $user = Auth::user();
        $worker = Worker::where('user_id', $user->id)->first();

        $order_offers_keys = 'emergency_order_offers_' . $request->input('order_id');
        $worker_offer_key = 'emergency_order_offers_' . $request->input('order_id') . '_' . $worker->id;


        if (!Cache::has($worker_offer_key)) {

            $new_offer = (new WinchOrderOfferResource($worker));

            if (Cache::has($order_offers_keys)) {
                $offers = Cache::get($order_offers_keys);
            } else {
                $offers = [];
            }

            array_unshift($offers, $worker_offer_key);

            Cache::put($order_offers_keys, $offers, 900);
            Cache::put($worker_offer_key, $new_offer, 900);

            Cache::forget('emergency_request_' . $worker->id . '_' .  $request->input('order_id'));

            return true;
        }

        return false;
    }

    public function checkForOrders()
    {
        $user = Auth::user();
        $worker = $user->worker;


        $accepted_offer = null;
        $accepted_offer_key = 'accepted_emergency_request_' . $worker->id;
        if (Cache::has($accepted_offer_key)) {
            $accepted_offer = Cache::get($accepted_offer_key);
        }

        $requests = [];
        if ($accepted_offer == null) {
            $worker_key = 'emergency_request_' . $worker->id;
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

    public function listServices()
    {
        return Service::all()->select(['id', 'name', 'price']);
    }

    public function updateOrderServices(UpdateOrderServicesRequest $request)
    {
        $services_ids = $request->input('services.*.id');

        $services = Service::select(['id', 'price'])->whereIn('id', $services_ids)->get()->keyBy('id')->toArray();

        $order_services_total = array_sum(array_column($services, 'price'));

        $order = Order::find($request->input('order_id'));
              //  $order->services()->sync($services);

        $pivot_data = [];
        foreach ($services as $id => $service) {
            $pivot_data[$id] = ['price' => $service['price']];
        }
        $order->services()->sync($pivot_data);

        $order->update([
            'services_price' => $order->services_price + $order_services_total,
            'total' =>  $order->total + $order_services_total,
        ]);

        $order_vendor = OrderVendor::where('order_id', $order->id)->first();
        $order_vendor->update([
            'services_price' => $order_vendor->services_price + $order_services_total,
            'total' =>  $order_vendor->total + $order_services_total,
        ]);
        
        
        $worker = Auth::user()->worker;
        $order_key = 'accepted_emergency_request_' . $worker->id;
        Cache::forget($order_key);

        return new EmergencyOrderResource($order);
    }
}
