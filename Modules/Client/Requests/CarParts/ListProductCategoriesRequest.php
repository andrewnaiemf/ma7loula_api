<?php
namespace Modules\Client\Requests\CarParts;

use Illuminate\Validation\Rule;
use Modules\Core\Requests\PublicRequest;

class ListProductCategoriesRequest extends PublicRequest{
    public function rules(): array
    {
        return
            [
                'category_id' => [
                    'nullable', Rule::exists('product_categories', 'id')->whereNull('deleted_at')
                ],
            ];
    }
}