<?php

namespace Modules\Client\Services;

use App\Models\Product;
use App\Models\ProductBrand;
use Illuminate\Http\Request;
use Modules\Client\Requests\Battery\ListBatteriesRequest;
use Modules\Client\Requests\Battery\ListBrandsRequest;
use Modules\Client\Requests\Battery\ListVoltRequest;
use Modules\Client\Resources\ProductBrandResource;

class BatteryService
{

    private function batteryProductQuery(Request $request)
    {
        return Product::where('products.category_id', Product::BatteriesCategory)
            ->where('products.status', 'published')
            ->whereHas('cars', function ($q) use ($request) {
                $q->where('id', $request->input('car_id'));
            });
    }

    public function listVoles(ListVoltRequest $request)
    {
        $batteries_products_volatages  = $this->batteryProductQuery($request)
            ->join('product_attributes as volt', function ($q) {
                $q->on('volt.product_id', 'products.id');
                $q->where('volt.key', 'voltage');
            })
            ->groupBy('volt.value')
            ->get()
            ->pluck('value');

        return $batteries_products_volatages;
    }

    public function listBrands(ListBrandsRequest $request)
    {
        $tires_products_brands  = $this->batteryProductQuery($request)
            ->whereHas('attrs', function ($q) use ($request) {
                $q
                    ->where('key', 'voltage')
                    ->where('value', $request->input('voltage'));
            })
            ->groupBy('brand_id')
            ->get()
            ->pluck('brand_id');

        $brands = ProductBrand::whereIn('id', $tires_products_brands)->get();

        return ProductBrandResource::collection($brands);
    }


    public function listBatteries(ListBatteriesRequest $request)
    {

        return $this->batteryProductQuery($request)
            ->where('products.brand_id', $request->input('brand_id'))
            ->whereHas('attrs', function ($q) use ($request) {
                $q
                    ->where('key', 'voltage')
                    ->where('value', $request->input('voltage'));
            });
    }
}
