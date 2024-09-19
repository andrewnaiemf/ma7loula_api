<?php

namespace Modules\Core\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class Controller
{
    public function successResponse(array|Collection $data = [], string $message = null): JsonResponse{
        return response()->json([
            'message' => $message??"",
            'data' => (object) $data
        ]);
    }
}