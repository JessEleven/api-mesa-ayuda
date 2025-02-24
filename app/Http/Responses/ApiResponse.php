<?php

namespace App\Http\Responses;

use Symfony\Component\HttpFoundation\Response;

class ApiResponse {

    // Response::$statusTexts[$statusCode] ?? "Unknown Status" es para obtener
    // los textos estándar de los códigos de estado HTTP de manera dinámica

    // Para los métodos index()
    public static function index($message, $statusCode, $data = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> true,
            "message"=> $message,
            "status_code"=> $statusCode,
            "results"=> [
                "total_elements"=> $data->total(),
                "pagination"=> [
                    "page"=> [
                        "current_page"=> $data->currentPage(),
                        "elements_per_page"=> $data->perPage(),
                        "total_pages"=> $data->lastPage()
                    ],
                    "positions"=> [
                        "first_item"=> $data->firstItem(),
                        "last_item"=> $data->lastItem()
                    ]
                ],
                "links"=> self::formatPaginationLinks($data),
                "data"=> $data->items(),
            ],
        ], $statusCode);
    }

    // Método auxiliar para formatear los links de la paginación
    private static function formatPaginationLinks($data) {
        return [
            "base_url"=> $data->path(),
            "previous"=> [
                "url"=> $data->previousPageUrl(),
                "label"=> "Previous",
                // Solo se activa si no se esta en la primera página
                "active"=> $data->currentPage() > 1
            ],
            "next"=> [
                "url"=> $data->nextPageUrl(),
                "label"=> "Next",
                // Solo se activo si no se esta en la última página
                "active"=> $data->currentPage() < $data->lastPage()
            ]
        ];
    }

    // Para los métodos store()
    public static function created($message, $statusCode, $data = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> true,
            "message"=> $message,
            "status_code"=> $statusCode,
            "data"=> $data
        ], $statusCode);
    }

    // Para los métodos show()
    public static function show($message, $statusCode, $data = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> true,
            "message"=> $message,
            "status_code"=> $statusCode,
            "data"=> $data
        ], $statusCode);
    }

    // Para los métodos update()
    public static function updated($message, $statusCode, $data = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> true,
            "message"=> $message,
            "status_code"=> $statusCode,
            "data"=> $data
        ], $statusCode);
    }

    // Para los métodos destroy()
    public static function deleted($message, $statusCode, $data = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> true,
            "message"=> $message,
            "status_code"=> $statusCode,
            "links"=> $data
        ], $statusCode);
    }

    // Para los métodos update() cuando no se actualiza nada, los campos
    public static function notUpdated($message, $statusCode = 200, $data = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> false,
            "message"=> $message,
            "status_code"=> $statusCode,
            "data"=> $data
        ], $statusCode);
    }

    // Para todo los métodos index(), store(), show(), etc...
    public static function error($message, $statusCode, $data = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> false,
            "message"=> $message,
            "status_code"=> $statusCode,
            "data"=> $data
        ], $statusCode);
    }

    // Para los Form Requests tanto para crear o actulizar los campos
    public static function validation($message, $statusCode, $errors = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> false,
            "message"=> $message,
            "status_code"=> $statusCode,
            "errors"=> $errors
        ], $statusCode);
    }

    // Para cuando una ruta no coincida con ninguna definida en el archivo de routes/api.php
    public static function pathNotFound($message, $statusCode, $hint = []) {
        return response()->json([
            "status"=> Response::$statusTexts[$statusCode] ?? "Unknown Status",
            "success"=> false,
            "message"=> $message,
            "status_code"=> $statusCode,
            "hint"=> $hint
        ], $statusCode);
    }
}
