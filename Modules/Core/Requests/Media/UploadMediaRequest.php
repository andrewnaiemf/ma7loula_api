<?php

namespace Modules\Core\Requests\Media;

use Modules\Core\Requests\PublicRequest;
use Illuminate\Validation\Rules\File;

class UploadMediaRequest extends PublicRequest
{
    public function rules(): array{
        return [
            'media' => ['required', 'array'],
            'media.*' => ['required']
        ];
    }
}