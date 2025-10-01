<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function apiResponse($success, $message, $data)
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data'    => $data,
        ], $success ? 200 : 400);
    }
}
