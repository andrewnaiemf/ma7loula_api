<?php

namespace Modules\Vendor\Services;

use App\Enums\OrderVendorLineStatus;
use App\Jobs\SendCustomerOrderStatusPushJob;
use App\Models\Media;
use App\Models\Order;
use App\Models\OrderVendor;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\Client\Resources\ProductBrandResource;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Services\AuthService;
use Modules\Vendor\Requests\BT\Vendor\AddBatteryRequest;
use Modules\Vendor\Requests\BT\Vendor\AddTireRequest;
use Modules\Vendor\Requests\BT\Vendor\ListOrdersRequest;
use Modules\Vendor\Requests\BT\Vendor\OrdersDetailsRequest;
use Modules\Vendor\Requests\BT\Vendor\ProductDetailsRequest;
use Modules\Vendor\Requests\BT\Vendor\RegisterRequest;
use Modules\Vendor\Requests\CarParts\AcceptVendorOrderLineRequest;
use Modules\Vendor\Requests\CarParts\SubmitVendorPriceOfferRequest;
use Modules\Vendor\Requests\CarParts\UpdateOrderStatusRequest;
use Modules\Vendor\Resources\BT\Vendor\OrderResource;
use Modules\Vendor\Resources\BT\Vendor\ProductResource;
use Modules\Vendor\Resources\BT\Vendor\VendorUserResource;

class BTVendorService extends AuthService
{
    public function __construct(
        private AuthService $authService,
        private VendorOrderLineActionService $orderLineActions,
    ) {}

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
        return new VendorUserResource($user);
    }

    public function registerVendor(RegisterRequest $request): JsonResource
    {
        $data = $request->all(['name', 'email', 'phone', 'password']);
        $data['role_id'] = 4;

        //create user
        $user =  User::create($data);
        $this->persistFcmTokenIfPresent($request, $user);
        $user->auth_token = $user->createToken('auth', ['*'], Carbon::now()->addDays(120))->plainTextToken;

        //handle media
        $files_keys = ['id_image', 'company_licence_image'];
        if (!is_dir(storage_path('app/public/bt-vendor/'))) {
            mkdir(storage_path('app/public/bt-vendor/'));
        }
        foreach ($files_keys as $key) {
            if ($request->input($key)) {
                $filename = $request->input($key);
                $path = storage_path('app/public/temp/' . $filename);
                $new_path = storage_path('app/public/bt-vendor/' . $filename);
                if (file_exists($path)) {
                    rename($path, $new_path);
                }
            }
        }

        //create vendor
        Vendor::create([
            'name' => $request->input('company_name'),
            'user_id' => $user->id,
            'type' => 'bt-vendor',
            'lat' => $request->input('lat'),
            'lon' => $request->input('lon'),
            'tax_no' => $request->input('tax_no'),
            'company_licence_no' => $request->input('company_licence_no'),
            'company_licence_expire_date' => $request->input('company_licence_expire_date'),
            'address' => $request->input('address'),
            'id_image' => $request->input('id_image'),
            'company_licence_image' => $request->input('company_licence_image'),
        ]);

        return new VendorUserResource($user);
    }

    public function listProducts($type, $status)
    {
        $vendor_id = Auth::user()->vendor->id;

        $products = Product::with(['brand', 'category', 'vendor', 'thumbnail', 'allMedia', 'attrs'])
            ->where('status', $status)
            ->where('vendor_id', $vendor_id)
            ->orderBy('id', 'desc');

        if ($type) {
            $category_id = ($type == 'batteries') ? Product::BatteriesCategory : Product::TiresCategory;
            $products->where('category_id', $category_id);
        }

        return $products;
    }


    public function addProduct(Request $request, $category_id): Product
    {
        $product_data = $request->all(['sku','name', 'description', 'brand_id', 'price', 'price_before_discount', 'stock']);

        $product_data = array_merge($product_data, [
            'vendor_id' => Auth::user()->vendor->id,
            'category_id' => $category_id,
            'status' => 'pending',
        ]);

        $product = Product::create($product_data);

        //sync product cars
        $product->cars()->sync($request->input('car_ids'));

        //handle product media
        $images = $request->input('images');
        if (count($images) > 0) {
            $default_image = $request->input('default_image');
            $this->handleMedia($product, $images, $default_image);
        }

        //create product
        return $product;
    }


    public function addBattery(AddBatteryRequest $request)
    {
        $product = $this->addProduct($request, Product::BatteriesCategory);

        //handle product attributes
        $product_attributes = $request->all(['sku', 'year_of_manufacture', 'voltage']);
        $this->handleProductAttributes($product, $product_attributes);

        return new ProductResource($product);
    }


    public function addTire(AddTireRequest $request)
    {
        $product = $this->addProduct($request, Product::TiresCategory);

        //handle product attributes
        $product_attributes = $request->all(['sku', 'year_of_manufacture', 'height', 'width', 'length', 'tire_type']);
        $this->handleProductAttributes($product, $product_attributes);

        return new ProductResource($product);
    }

    public function destroy(Request $request){
        Product::where('id',$request->id)->delete();
        return;
    }
    private function handleProductAttributes(Product $product, array $product_attributes)
    {
        $attr = [];
        foreach ($product_attributes as $key => $val) {
            if ($val)
                $attr[] = [
                    'product_id' => $product->id,
                    'key' => $key,
                    'value' => $val
                ];
        }
        $product->attrs()->createMany($attr);
    }

    private function handleMedia(Product $product, array $images, string $default_image = null)
    {
        $path = 'app/public/products/';

        if (!is_dir(storage_path($path))) {
            mkdir(storage_path($path));
        }

        $media = [];
        $default_image_id = null;

        if ($default_image == null) {
            $default_image = $images[0];
        }

        foreach ($images as $filename) {
            $filepath = storage_path('app/public/temp/' . $filename);
            $fileNewPath = storage_path($path . $filename);
            if (file_exists($filepath)) {
                rename($filepath, $fileNewPath);
                $item = [
                    'model_type' => Product::class,
                    'model_id' => $product->id,
                    'path' => 'storage/products/' . $filename,
                    'filename' => $filename
                ];

                if ($filename != $default_image) {
                    $media[] =  $item;
                } else {
                    $default_image_id = Media::create($item)->id;
                    $product->update(['default_media_id' => $default_image_id]);
                }
            }
        }

        if ($media) {
            Media::insert($media);
        }
    }

    public function productDetails(ProductDetailsRequest $request)
    {
        $vendor_id = Auth::user()->vendor->id;

        $product = Product::with(['brand', 'category', 'vendor', 'thumbnail', 'allMedia', 'attrs'])
            ->where('vendor_id', $vendor_id)
            ->where('id', $request->input('id'))
            ->whereNull('deleted_at')
            ->first();

        return new ProductResource($product);
    }

    public function listOrders(ListOrdersRequest $request, $status)
    {
        $vendor_id = Auth::user()->vendor->id;

        $orders = OrderVendor::with([
            'order.products' => function ($q) use ($vendor_id) {
                $q->where('products.vendor_id', $vendor_id);
            },
            'order.products.thumbnail',
            'order.user_car',
            'order.user_car.car',
            'order.user_car.car.model',
            'order.user_car.car.model.brand',
            'order.user_car.client'
        ])
            ->orderBy('id', 'desc')
            ->where('vendor_id', $vendor_id);

        if ($status === 'active') {
            $orders->whereNotIn('status', [
                OrderVendorLineStatus::Completed->value,
                OrderVendorLineStatus::Cancelled->value,
            ]);
        } elseif (in_array($status, ['completed', 'history'], true)) {
            $orders->whereIn('status', [
                OrderVendorLineStatus::Completed->value,
                OrderVendorLineStatus::Cancelled->value,
            ]);
        } else {
            $orders->where('status', $status);
        }

        return $orders;
    }

    public function listBrands($type)
    {
        $brands = ProductBrand::query();

        if($type == 'batteries'){
            $brands->where('product_category_id', Product::BatteriesCategory);
        }else{
            $brands->where('product_category_id', Product::TiresCategory);
        }

        $brands = $brands->get();

        return ProductBrandResource::collection($brands);
    }

    public function orderDetails(OrdersDetailsRequest $request){
        $vendor_id = Auth::user()->vendor->id;

        $order = OrderVendor::with([
            'order.products' => function ($q) use ($vendor_id) {
                $q->where('products.vendor_id', $vendor_id);
            },
            'order.products.thumbnail',
            'order.user_car',
            'order.user_car.car',
            'order.user_car.car.model',
            'order.user_car.car.model.brand',
            'order.user_car.client'
        ])
        ->where('vendor_id', $vendor_id)
        ->where('id', $request->input('id'))
        ->first();

        return new OrderResource($order);
    }

    public function updateOrderStatus(UpdateOrderStatusRequest $request){
        $order = OrderVendor::find($request->input('id'));
        if (! $order) {
            throw new HttpErrorException(__('Order line not found.'), [], 404);
        }
        $previousStatus = (string) $order->status;
        $order->update([
            'status' => $request->input('status')
        ]);
        if ($previousStatus !== (string) $order->status) {
            SendCustomerOrderStatusPushJob::dispatch((int) $order->id);
        }
        return new OrderResource($order);
    }

    public function acceptOrderLine(AcceptVendorOrderLineRequest $request): OrderResource
    {
        $vendorId = Auth::user()->vendor->id;
        $line = $this->orderLineActions->acceptAtListedPrice(
            (int) $request->input('order_vendor_id'),
            $vendorId
        );

        return new OrderResource($line);
    }

    public function submitPriceOffer(SubmitVendorPriceOfferRequest $request): OrderResource
    {
        $vendorId = Auth::user()->vendor->id;
        $line = $this->orderLineActions->submitCounterOffer(
            (int) $request->input('order_vendor_id'),
            $vendorId,
            (string) $request->input('offered_total')
        );

        return new OrderResource($line);
    }
}
