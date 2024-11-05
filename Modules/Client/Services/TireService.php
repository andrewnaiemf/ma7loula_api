<?php

namespace Modules\Client\Services;

use App\Models\Product;
use App\Models\ProductBrand;
use Illuminate\Http\Request;
use Modules\Client\Requests\Tire\ListBrandsRequest;
use Modules\Client\Requests\Tire\ListSizesRequest;
use Modules\Client\Requests\Tire\ListTiresRequest;
use Modules\Client\Requests\Tire\ListTypesRequest;
use Modules\Client\Resources\ProductBrandResource;

class TireService
{

    private function tireProductQuery(Request $request)
    {
        return Product::where('products.category_id', Product::TiresCategory)
            ->where('products.status', 'published')
            ->whereHas('cars', function ($q) use ($request) {
                $q->where('id', $request->input('car_id'));
            });
    }

    public function listBrands(ListBrandsRequest $request)
    {
        $tires_products_brands  = $this->tireProductQuery($request)
            ->groupBy('brand_id')
            ->get()
            ->pluck('brand_id');

        $brands = ProductBrand::whereIn('id', $tires_products_brands)->get();

        return ProductBrandResource::collection($brands);
    }

    public function listTypes(ListTypesRequest $request)
    {
        $tires_products_types  = $this->tireProductQuery($request)
            ->select('product_attributes.value')
            ->join('product_attributes', 'product_attributes.product_id', 'products.id')
            ->where('products.brand_id', $request->input('brand_id'))
            ->where('product_attributes.key', 'tire_type')
            ->groupBy('product_attributes.value')
            ->get()
            ->pluck('value');

        return $tires_products_types;
    }

    public function listSizes(ListSizesRequest $request)
    {
        $tires_products_sizes  = $this->tireProductQuery($request)
            ->select(['height.value as height', 'width.value as width', 'length.value as length'])
            ->where('products.brand_id', $request->input('brand_id'))
            ->whereHas('attrs', function ($q) use ($request) {
                $q
                    ->where('key', 'tire_type')
                    ->where('value', $request->input('type'));
            })
            ->join('product_attributes as height', function ($q) {
                $q->on('height.product_id', 'products.id');
                $q->where('height.key', 'height');
            })
            ->join('product_attributes as width', function ($q) {
                $q->on('width.product_id', 'products.id');
                $q->where('width.key', 'width');
            })
            ->join('product_attributes as length', function ($q) {
                $q->on('length.product_id', 'products.id');
                $q->where('length.key', 'length');
            })
            ->groupBy(['height.value', 'width.value', 'length.value'])
            ->get();

        return $tires_products_sizes;
    }

    public function listTires(ListTiresRequest $request)
    {
        return $this->tireProductQuery($request)
            ->where('products.brand_id', $request->input('brand_id'))
            ->whereHas('attrs', function ($q) use ($request) {
                $q
                    ->where('key', 'tire_type')
                    ->where('value', $request->input('type'));
            })
            ->whereHas('attrs', function ($q) use ($request) {
                $q
                    ->where('key', 'height')
                    ->where('value', $request->input('height'));
            })
            ->whereHas('attrs', function ($q) use ($request) {
                $q
                    ->where('key', 'width')
                    ->where('value', $request->input('width'));
            })
            ->whereHas('attrs', function ($q) use ($request) {
                $q
                    ->where('key', 'length')
                    ->where('value', $request->input('length'));
            });
    }
}
