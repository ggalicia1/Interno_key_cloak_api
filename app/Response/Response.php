<?php

namespace App\Response;

class Response
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public static function success($status, $message, $data, $code)
    {
        return response()->json([
                                    'status' => $status,
                                    'message' => $message,
                                    'data' => $data,

                                ], $code);
    }

    public static function error($status, $message, $code)
    {
        return response()->json([
                                    'status' => false,
                                    'message' => $message
                                ], $code);
    }
    public static function errorMessage($status, $message, $error_message, $code)
    {
        return response()->json([
                                    'status' => false,
                                    'message' => $message,
                                    'error' => $error_message
                                ], $code);
    }

    

}
