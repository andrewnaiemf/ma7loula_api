<?php

namespace Modules\Client\Requests\EmergencyOrder;

use Illuminate\Validation\Rule;
use Modules\Core\Exceptions\HttpErrorException;
use Modules\Core\Requests\PublicRequest;

class CreateEmergencyOrderRequest extends PublicRequest
{
    public function rules(): array
    {
        return [
            'user_car_id' => ['required', Rule::exists('user_car', 'id')->whereNull('deleted_at')],
            'description' => ['nullable', 'string'],
            'record' => ['nullable', 'string']
        ];
    }

    protected function passedValidation(): void
    {
        $errors = [];
        if ($this->input('record')) {
            $filename = $this->input('record');
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $path = storage_path('app/public/temp/' . $filename);

            if (!file_exists($path)) {
                $errors['record'] = 'file upload error';
            }

            /* if (!in_array($ext, ['mp3', 'wav'])) {
                $errors['images'][] = 'unsupproted file extension';
            } */

            if (count($errors)) {
                throw new HttpErrorException('Images error', $errors, 422);
            }
        }
    }
}
