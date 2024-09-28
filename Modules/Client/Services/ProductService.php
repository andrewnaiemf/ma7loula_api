<?php

namespace Modules\Client\Services;

use App\Models\Product;
use Modules\Client\Requests\CarParts\ListProductsRequest;
use Modules\Client\Resources\ProductResource;

class ProductService
{
    public function listProducts(ListProductsRequest $request){
        
        $products = Product::with(['brand', 'category', 'vendor', 'thumbnail', 'allMedia'])->where('status', 'published');
        
        if($request->input('category_id')){
            $products->where('category_id', $request->input('category_id'));
        }

        if($request->input('name')){
            $products->where('name', 'like', '%'.$request->input('name').'%');
        }


        return $products;

    }
}
