<?php

namespace Modules\Core\Services;

use Modules\Core\Requests\Media\UploadMediaRequest;

class MediaService
{
    public function uploadMedia(UploadMediaRequest $req)
    {
        $files = $req->file('media');

        $res = [];

        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            $randName = sha1(time() . rand(99999, 99999999)) . '.' . $ext;
            $file->storeAs('temp', $randName, 'public');
            $res[] = [
                'filename' => $randName,
                'temp_url' => url('storage/temp/' . $randName),
            ];
        }

        return $res;
    }
}
