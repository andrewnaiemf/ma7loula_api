<?php

namespace Modules\Winch\Requests;

use Illuminate\Validation\Rule;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Requests\Auth\RegisterRequest as AuthRegisterRequest;

class RegisterRequest extends AuthRegisterRequest
{
    public function rules(): array
    {
        return array_merge([
            'id_image' => ['required', 'string'],
            'criminal_record_image' => ['required', 'string'],
            
            'driver_licence_image' => ['required', 'string'],
            'driver_licence_no' => ['required', 'numeric'],
            'driver_licence_expire_date' => ['date_format:Y-m-d', 'string'],

            'car_licence_image' => ['required', 'string'],
            'car_licence_no' => ['required', 'numeric'],
            'car_licence_expire_date' => ['date_format:Y-m-d', 'string'],

            'vendor_id' => ['required', Rule::exists('vendors', 'id')->where('type', 'winch')->whereNull('deleted_at')],
            'car_plate_no' => ['required', 'string'],
            
        ], parent::rules());
    }

    protected function passedValidation(): void
    {
        parent::passedValidation();

        $files_keys = ['id_image', 'criminal_record_image', 'car_licence_image', 'driver_licence_image'];
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
