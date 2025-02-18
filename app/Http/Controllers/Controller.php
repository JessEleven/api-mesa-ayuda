<?php

namespace App\Http\Controllers;

use Str;

abstract class Controller
{
    // Para reuilizar en los demás controladores (métodos destroy)
    protected function getRelativePath()
    {
        // Devuelve la ruta relativa actual de manera dinámica
        $path = request()->path();
        // Expresión regular para remover el ID
        $onlyPath = preg_replace("/\/[^\/]+$/", "", $path);
        return "/" . $onlyPath;
    }

    protected function getApiVersion()
    {
        $routeName = request()->route()->getPrefix();

        // Expresión regular para extraer la versión de la API (ej. v1, v2, etc)
        if (preg_match('/api\/(v[0-9]+)/', $routeName, $matches)) {
            return $matches[1];
        }
        // Si no éxiste se devueleve un valor por defecto
        return "No existe";
    }
}
