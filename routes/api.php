<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\BitacoraTicketController;
use App\Http\Controllers\CalificacionTicketController;
use App\Http\Controllers\CategoriaTicketController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\EstadoTicketController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\PrioridadTicketController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\RolPermisoController;
use App\Http\Controllers\TecnicoAsignadoController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UsuarioRolController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('area', AreaController::class);
Route::apiResource('bitacora', BitacoraTicketController::class);
Route::apiResource('calificacion-ticket', CalificacionTicketController::class);
Route::apiResource('categoria-ticket', CategoriaTicketController::class);
Route::apiResource('departamento', DepartamentoController::class);
Route::apiResource('estado-ticket', EstadoTicketController::class);
Route::apiResource('permiso', PermisoController::class);
Route::apiResource('prioridad-ticket', PrioridadTicketController::class);
Route::apiResource('rol', RolController::class);
Route::apiResource('rol-permiso', RolPermisoController::class);
Route::apiResource('tecnico-asignado', TecnicoAsignadoController::class);
Route::apiResource('ticket', TicketController::class);
Route::apiResource('usuario', UsuarioController::class);
Route::apiResource('usuario-rol', UsuarioRolController::class);

