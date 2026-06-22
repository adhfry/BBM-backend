<?php

if (! function_exists('apiSuccess')) {
    /**
     * Format standarisasi untuk response sukses (2xx)
     */
    function apiSuccess($data = [], string $message = 'Success', int $statusCode = 200, array $meta = [])
    {
        return response()->json([
            'status'     => 'success',
            'data'       => $data,
            'message'    => $message,
            'statusCode' => $statusCode,
            'meta'       => empty($meta) ? null : $meta,
        ], $statusCode);
    }
}

if (! function_exists('apiError')) {
    /**
     * Format standarisasi untuk response error (4xx / 5xx)
     */
    function apiError(string $message = 'Error', int $statusCode = 400, $data = [])
    {
        return response()->json([
            'status'     => 'error',
            'data'       => empty($data) ? null : $data,
            'message'    => $message,
            'statusCode' => $statusCode,
            'meta'       => null,
        ], $statusCode);
    }
}
