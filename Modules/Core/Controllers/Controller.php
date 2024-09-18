<?php

namespace Modules\Core\Controllers;

use Illuminate\Http\JsonResponse;

class Controller
{
    public function successResponse(array $data = [], string $message = null): JsonResponse{
        return response()->json([
            'message' => $message,
            'data' => (object) $data
        ]);
    }
}