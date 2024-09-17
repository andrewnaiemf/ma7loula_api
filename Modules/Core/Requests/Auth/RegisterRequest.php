<?php

namespace Modules\Core\Requests\Auth;

use Modules\Core\Requests\PublicRequest;

class RegisterRequest extends PublicRequest
{
    public function rules(): array{
        return [
            ''
        ];
    }
}