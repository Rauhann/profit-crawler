<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

abstract class Controller
{
    protected function toJson(
        mixed $data,
        string|int $statusCode = 200
    ): JsonResponse {
        if (is_string($statusCode)) {
            $statusCode = 400;
        }
        if ($statusCode == 201 || is_null($data)) {
            $data = [];
        }
        if ($statusCode == 0) {
            $statusCode = 400;
        }
        return response()->json($data, $statusCode);
    }
}
