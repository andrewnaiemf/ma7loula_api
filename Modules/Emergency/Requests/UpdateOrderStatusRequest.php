<?php

namespace Modules\Emergency\Requests;

use App\Models\OrderVendor;
use Modules\Core\Requests\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->worker && OrderVendor::where('order_id', $this->id)->where('worker_id', Auth::user()->worker->id)->exists();
    }


    public function rules(): array
    {
        $worker = Auth::user()->worker;
        return
            [
                'id' => [
                    'required', Rule::exists('order_vendors', 'order_id')->where('worker_id', $worker->id)->whereNull('deleted_at')
                ],
                'status' => [
                    'required', 'string'
                ]
            ];
    }
}
