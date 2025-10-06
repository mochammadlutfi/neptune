<?php

namespace App\Services\Base;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ApiResponseService
{
    public function success(mixed $data = null, ?string $message = null, mixed $meta = null): JsonResponse
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
            'meta'    => $meta,
        ]);
    }

    public function error(string $message = 'Something went wrong', int $code = 400, mixed $errors = null): JsonResponse
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'errors'  => $errors,
        ], $code);
    }

    public function paginate(LengthAwarePaginator $collection): JsonResponse
    {
        return $this->success(
            $collection->items(),
            null,
            [
                'current_page' => $collection->currentPage(),
                'per_page'     => $collection->perPage(),
                'total'        => $collection->total(),
                'last_page'    => $collection->lastPage(),
            ]
        );
    }
}
