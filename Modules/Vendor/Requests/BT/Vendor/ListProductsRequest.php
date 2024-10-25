<?php

namespace Modules\Vendor\Requests\BT\Vendor;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;

class ListProductsRequest extends Request
{
    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->vendor;
    }
    
    public function rules(): array
    {
        return [];
    }
}
