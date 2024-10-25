<?php
namespace Modules\Core\Controllers;

use Modules\Core\Requests\Media\UploadMediaRequest;
use Modules\Core\Services\MediaService;

class MediaController extends Controller{

    public function __construct(private MediaService $mediaService)
    {
        
    }

    public function uploadMedia(UploadMediaRequest $req){
        return $this->successResponse([
            'images' => $this->mediaService->uploadMedia($req)
        ]);
    }
    
}