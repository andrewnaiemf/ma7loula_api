<?php
namespace Modules\Client\Requests\CarParts;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ListProductsRequest extends PublicRequest{
    public function rules(): array
    {
        return
            [
                'category_id' => [
                    'required', Rule::exists('product_categories', 'id')->whereNull('deleted_at')
                ],
            ];
    }
}