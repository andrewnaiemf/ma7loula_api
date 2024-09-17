<?php

namespace Modules\Core\Controllers;

use Illuminate\Http\JsonResponse;

class Controller
{
    public function successResponse(string $message, array $data = []): JsonResponse{
        return response()->json([
            'message' => $message,
            'data' => $data
        ]);
    }
}