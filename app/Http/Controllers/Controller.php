<?php

namespace App\Http\Controllers;

abstract class Controller
{
    //Para reuilizar en los demás controladores (métodos destroy())
    protected function getBaseRoute()
    {
        // Devuelve la ruta relativa actual de manera dinámica
        $path = request()->path();
        return preg_replace('/\/[^\/]+$/', '', $path);
    }
}
