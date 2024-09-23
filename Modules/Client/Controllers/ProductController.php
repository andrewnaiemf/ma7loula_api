<?php
namespace Modules\Client\Controllers;

use App\Models\ProductCategory;
use Modules\Client\Requests\CarParts\ListProductsRequest;
use Modules\Client\Resources\ProductCategoryResource;
use Modules\Core\Controllers\Controller;

class ProductController extends Controller{

    public function list(ListProductsRequest $request){
        $productCategories = ProductCategory::with(['media', 'subCategories', 'subCategories.media']);

        if($request->input('category_id')){
            $productCategories->where('id', $request->input('category_id'));
        }else{
            $productCategories->whereNull('parent_id');
        }

        return $this->successResponse([
            'productCategories' => ProductCategoryResource::collection($productCategories->get())
        ]);
    }
}