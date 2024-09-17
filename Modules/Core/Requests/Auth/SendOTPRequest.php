<?php

namespace Modules\Core\Requests\Auth;

use Modules\Core\Requests\PublicRequest;

class SendOTPRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015']
        ];
    }
}