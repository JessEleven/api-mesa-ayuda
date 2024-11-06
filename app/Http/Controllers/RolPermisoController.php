<?php

namespace App\Http\Controllers;

use App\Models\RolPermiso;
use Illuminate\Http\Request;

class RolPermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rolPermiso = RolPermiso::all();
        return response()->json([
            "rol_permiso_junin"=> $rolPermiso
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
    public function show(RolPermiso $rolPermiso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RolPermiso $rolPermiso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RolPermiso $rolPermiso)
    {
        //
    }
}
