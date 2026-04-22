<?php

namespace Modules\Core\Requests\Auth;

use Illuminate\Validation\Rule;
use Modules\Core\Helpers\AppToRole;
use Illuminate\Database\Eloquent\Builder;

class ResetPasswordRequest extends ValidateOTPRequest
{
    public function rules(): array{
        $app = $this->header('App');
        
        $phoneRule = ['required', 'digits:11', 'starts_with:011,010,012,015'];
        
        // If request is from client app, check if user is a client
        // Otherwise, allow any user regardless of role
        if ($app === 'client') {
            $phoneRule[] = Rule::exists('users', 'phone')
                ->where('role_id', AppToRole::getRoleId('client'))
                ->whereNull('deleted_at');
        } else {
            $phoneRule[] = Rule::exists('users', 'phone')
                ->whereNull('deleted_at');
        }
        
        return array_merge([
            'phone' => $phoneRule,
            'password' => ['required', 'min:8', 'confirmed'],
        ], parent::rules());
    }
}