<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Support\Facades\Cache;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Requests\PublicRequest;

class ValidateOTPRequest extends PublicRequest
{
    public function rules(): array
    {
        return [
            'otp' => ['required', 'digits:6'],
        ];
    }

    protected function passedValidation(): void
    {
        if ($this->input('phone')) {
            if (!Cache::has('otp_for_' . $this->input('phone')) || Cache::get('otp_for_' . $this->input('phone')) !=  $this->input('otp')) {
                throw new HttpErrorException(trans('Core::messages.auth.otp_wrong'), [], 422);
            }
        }
    }
}
