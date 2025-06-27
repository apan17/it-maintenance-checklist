<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function success($message = "Berjaya", $data = null)
    {
        return response()->json([
            'message' => $message,
            'data' => $data
        ], 200);
    }
}
