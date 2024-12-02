<?php

namespace App\Http\Responses;

class ApiResponse {

    public static function success($message= "Success", $statusCode= 200, $data= []) {

        // Detectar si $data es una instancia de LengthAwarePaginator (cuando se usa paginación)
        // Ya que si se busca un solo recurso "results" tomara "data"
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            return response()->json([
                "message"=> $message,
                "status_code"=> $statusCode,
                "error"=> false,
                "results"=> [
                    "pagination"=> [
                        "current_page"=> $data->currentPage(),
                        "last_page"=> $data->lastPage(),
                        "per_page"=> $data->perPage(),
                        "start_position"=> $data->firstItem(),
                        "end_position"=> $data->lastItem(),
                        "total"=> $data->total()
                    ],
                    "links"=> [
                        "next_page_url"=> $data->nextPageUrl(),
                        "prev_page_url"=> $data->previousPageUrl(),
                        "path" => $data->path()
                    ],
                    "data" => $data->items(),
                ],
            ], $statusCode);
        }

        // Para un solo recurso,"data" sera "data"
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
            "links"=> $data
        ], $statusCode);
    }
}
