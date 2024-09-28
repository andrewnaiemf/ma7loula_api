<?php

namespace Modules\Core\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

class Controller
{
    public function successResponse(array|Collection $data = [], string $message = null): JsonResponse
    {
        return response()->json([
            'message' => $message ?? "",
            'data' => (object) $data
        ]);
    }


    public function listResponse($key, Builder $query, JsonResource $resource = null, string $message = null): JsonResponse
    {
        $page = max(request('page', 1), 1);
        $perPage = min(max(request('perPage', 20), 1), 100);

        $data = (clone $query)->take($perPage)->skip(($page - 1) * $perPage)->get();
        $total = (clone $query)->count();

        return response()->json([
            'message' => $message ?? "",
            'pagination' => [
                'page' => (int) $page,
                'ofPages' => (int) ceil($total/$perPage),
                'perPage' => (int) $perPage,
                'total' => $total
            ],
            'data' => [
                $key => $resource ? $resource::collection($data) : $data
            ]
        ]);
    }
}
