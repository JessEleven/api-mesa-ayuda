<?php

namespace App\Http\Controllers;

use App\Models\UsuarioRol;
use Illuminate\Http\Request;

class UsuarioRolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarioRol = UsuarioRol::all();
        return response()->json([
            "usuario_rol_junin"=> $usuarioRol
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UsuarioRol $usuarioRol)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UsuarioRol $usuarioRol)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UsuarioRol $usuarioRol)
    {
        //
    }
}
