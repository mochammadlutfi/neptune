<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponseTrait
{
    /**
     * Return a success JSON response.
     */
    protected function success($data = null, string $message = null, int $code = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'result' => $data
        ], $code);
    }

    /**
     * Return an error JSON response.
     */
    protected function error(string $message, $errors = null, int $code = Response::HTTP_UNPROCESSABLE_ENTITY): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a validation error JSON response.
     */
    protected function validationError($errors, string $message = 'Validation failed'): JsonResponse
    {
        return $this->error($message, $errors, Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Return an unauthorized JSON response.
     */
    protected function unauthorized(string $message = 'Unauthorized action'): JsonResponse
    {
        return $this->error($message, null, Response::HTTP_FORBIDDEN);
    }

    /**
     * Return a not found JSON response.
     */
    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return $this->error($message, null, Response::HTTP_NOT_FOUND);
    }

    /**
     * Return an internal server error JSON response.
     */
    protected function serverError(string $message = 'Internal server error'): JsonResponse
    {
        return $this->error($message, null, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Return a created JSON response.
     */
    protected function created($data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return $this->success($data, $message, Response::HTTP_CREATED);
    }

    /**
     * Return a no content JSON response.
     */
    protected function noContent(): JsonResponse
    {
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}