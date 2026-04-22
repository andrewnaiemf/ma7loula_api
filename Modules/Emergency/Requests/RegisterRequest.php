<?php

namespace Modules\Emergency\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Requests\Auth\RegisterRequest as AuthRegisterRequest;

class RegisterRequest extends AuthRegisterRequest
{
    public function rules(): array
    {
        return array_merge([
            'email' => ['required', 'email', Rule::unique('users', 'email')->where('role_id', 5)->whereNull('deleted_at')],
            'phone' => ['required', 'digits:11', 'starts_with:011,010,012,015', Rule::unique('users', 'phone')->where('role_id', 5)->whereNull('deleted_at')],
            'password' => ['required', 'min:8', 'confirmed'],
            'id_image' => ['required', 'string'],
            'criminal_record_image' => ['required', 'string'],
            'vendor_id' => ['required', Rule::exists('vendors', 'id')->where('type', 'emergency')->whereNull('deleted_at')],
        ], parent::rules());
    }

    protected function passedValidation(): void
    {
        parent::passedValidation();

        $files_keys = ['id_image', 'criminal_record_image'];
        $errors = [];
        foreach ($files_keys as $key) {
            if ($this->input($key)) {
                $filename = $this->input($key);
                $ext = pathinfo($filename, PATHINFO_EXTENSION);
                $path = storage_path('app/public/temp/' . $filename);

                if (!file_exists($path)) {
                    $errors[$key][] = 'file required';
                }

                if (!in_array($ext, ['png', 'jpg', 'jpeg'])) {
                    $errors[$key][] = 'unsupproted file extension';
                }
            }
        }

        if (count($errors)) {
            throw new HttpErrorException('Files error', $errors, 422);
        }
    }
}
