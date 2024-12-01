<?php

namespace Modules\Winch\Requests;

use Illuminate\Support\Facades\Auth;
use Modules\Core\Requests\Request;

class UpdateLocationRequest extends Request
{

    public function authorize(): bool
    {
        return Auth::user() && Auth::user()->worker && Auth::user()->worker->type == 'winch';
    }

    public function rules(): array
    {
        return [
            'lat' => ['required'],
            'lon' => ['required'],
        ];
    }
}
