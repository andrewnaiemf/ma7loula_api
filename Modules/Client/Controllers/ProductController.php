<?php
namespace Modules\Client\Controllers;

use App\Models\ProductCategory;
use Modules\Client\Requests\CarParts\ListProductsRequest;
use Modules\Client\Resources\ProductResource;
use Modules\Client\Services\ProductService;
use Modules\Core\Controllers\Controller;

class ProductController extends Controller{

    public function __construct(private ProductService $productService)
    {
        
    }

    public function list(ListProductsRequest $request){
        return $this->listResponse('products', $this->productService->listProducts($request), new ProductResource([]));
    }
}