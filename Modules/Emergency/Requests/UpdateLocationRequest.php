<?php

namespace Modules\Emergency\Requests;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;

class UpdateLocationRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->worker && Auth::user()->worker->type == 'emergency';
    }

    public function rules(): array
    {
        return [
            'lat' => ['required'],
            'lon' => ['required'],
        ];
    }
}
