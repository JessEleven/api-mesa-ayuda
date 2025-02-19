<?php

namespace App\Services;

class UsuarioModelHider
{
    public static function hideUsuarioFields($user)
    {
        // Para ocultar los PKs y timestamps de las tablas relaciones
        $user->departamento?->makeHidden(["id", "created_at", "updated_at"]);
        $user->departamento?->area?->makeHidden(["id", "created_at", "updated_at"]);
        return $user;
    }
}
