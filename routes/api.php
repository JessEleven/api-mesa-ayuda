<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BitacoraTicketController;
use App\Http\Controllers\CalificacionTicketController;
use App\Http\Controllers\CategoriaTicketController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EstadoTicketController;
use App\Http\Controllers\PrioridadTicketController;
use App\Http\Controllers\TecnicoAsignadoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UsuarioController;

use App\Http\Responses\ApiResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::apiResource('area', AreaController::class);
    Route::apiResource('calificacion-ticket', CalificacionTicketController::class);
    Route::apiResource('categoria-ticket', CategoriaTicketController::class);
    Route::apiResource('departamento', DepartamentoController::class);
    Route::apiResource('estado-ticket', EstadoTicketController::class);
    Route::apiResource('prioridad-ticket', PrioridadTicketController::class);
    Route::apiResource('tecnico-asignado', TecnicoAsignadoController::class);
    Route::apiResource('ticket', TicketController::class);
    Route::apiResource('usuario', UsuarioController::class);
    Route::apiResource('bitacora-ticket', BitacoraTicketController::class);
});

// Para manejar las rutas mal definidas
Route::fallback(function () {
    $currentUrl = url()->current();
    $path = preg_replace('/\/[^\/]+$/', '', $currentUrl);

    return ApiResponse::pathNotFound(
        "La ruta proporcionada no es válida",
        404,
        ["path"=> $path]
    );
});
