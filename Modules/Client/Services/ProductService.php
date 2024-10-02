<?php

namespace Modules\Client\Services;

use App\Models\Product;
use Modules\Client\Requests\CarParts\ListProductsRequest;
use Modules\Client\Requests\CarParts\ProductsDetailsRequest;
use Modules\Client\Resources\ProductResource;
use Modules\Client\Resources\ProductWithAttributesResource;
use Modules\Core\Exceptions\HttpErrorException;

class ProductService
{
    public function listProducts(ListProductsRequest $request)
    {
        $products = Product::with(['brand', 'category', 'vendor', 'thumbnail', 'allMedia'])
            ->where('status', 'published')
            ->whereHas('cars', function ($q) use ($request) {
                $q->where('id', $request->input('car_id'));
            });

        if ($request->input('category_id')) {
            $products->where('category_id', $request->input('category_id'));
        }

        if ($request->input('name')) {
            $products->where('name', 'like', '%' . $request->input('name') . '%');
        }

        return $products;
    }

    public function productDetails(ProductsDetailsRequest $request)
    {
        $product = Product::with(['brand', 'category', 'vendor', 'thumbnail', 'allMedia', 'attrs'])
            ->where('status', 'published')
            ->where('id', $request->input('id'))
            ->first();


        if(!$product){
            throw new HttpErrorException("Not found");
        }

        return new ProductWithAttributesResource($product);
    }
}
