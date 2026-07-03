<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;

class ApiResponse
{
    /**
     * Response sukses standar.
     *
     * @param  mixed  $data
     */
    public static function success(
        mixed $data = null,
        string $message = 'Berhasil',
        int $code = 200
    ): JsonResponse {
        $response = [
            'status'  => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }

    /**
     * Response error standar.
     *
     * @param  mixed  $errors
     */
    public static function error(
        string $message = 'Terjadi kesalahan',
        mixed $errors = null,
        int $code = 400
    ): JsonResponse {
        $response = [
            'status'  => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Response 401 Unauthorized.
     */
    public static function unauthorized(string $message = 'Tidak terautentikasi'): JsonResponse
    {
        return self::error($message, null, 401);
    }

    /**
     * Response 403 Forbidden.
     */
    public static function forbidden(string $message = 'Akses ditolak'): JsonResponse
    {
        return self::error($message, null, 403);
    }

    /**
     * Response 422 Unprocessable (validation error).
     *
     * @param  mixed  $errors
     */
    public static function validationError(mixed $errors): JsonResponse
    {
        return self::error('Data tidak valid', $errors, 422);
    }

    /**
     * Response 500 Server Error.
     */
    public static function serverError(string $message = 'Terjadi kesalahan server'): JsonResponse
    {
        return self::error($message, null, 500);
    }
}
