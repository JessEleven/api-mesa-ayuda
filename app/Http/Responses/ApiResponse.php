<?php

namespace App\Http\Responses;

class ApiResponse {

    public static function success($message= "Success", $statusCode= 200, $data= []) {
        return response()->json([
            "message"=> $message,
            "status_code"=> $statusCode,
            "error"=> false,
            "data"=> $data
        ], $statusCode);
    }

    public static function error($message= "Error", $statusCode= 404, $data= []) {
        return response()->json([
            "message"=> $message,
            "status_code"=> $statusCode,
            "error"=> true,
            "data"=> $data
        ], $statusCode);
    }

    public static function validation($message= "Validation error", $statusCode= 422, $errors= []) {
        return response()->json([
            "message"=> $message,
            "status_code"=> $statusCode,
            "error"=> true,
            "errors"=> $errors
        ], $statusCode);
    }

    public static function deleted($message= "Resourse deleted", $statusCode= 200, $data= []) {
        return response()->json([
            "message"=> $message,
            "status_code"=> $statusCode,
            "error"=> false,
            "link"=> $data
        ], $statusCode);
    }
}
