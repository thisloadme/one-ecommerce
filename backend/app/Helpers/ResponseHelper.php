<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function basicResponse($code = 200, $data = [], $message = null, $error = null)
    {
        return response()->json([
            'code' => $code,
            'data' => $data,
            'message' => $message,
            'error'=> $error
        ], $code);
    }

    public static function serverError($message = null, $error = null)
    {
        return self::basicResponse(500, [], $message, $error);
    }

    public static function validationError($message = 'Validation failed', $errors = null)
    {
        return self::basicResponse(422, [], $message, $errors);
    }
}