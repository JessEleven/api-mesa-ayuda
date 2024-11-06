<?php

namespace App\Http\Controllers;

use App\Models\TecnicoAsignado;
use Illuminate\Http\Request;

class TecnicoAsignadoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tecnicoAsiganado = TecnicoAsignado::all();
        return response()->json([
            "tecnico_asiganado_junin"=> $tecnicoAsiganado
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
    public function show(TecnicoAsignado $tecnicoAsignado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TecnicoAsignado $tecnicoAsignado)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TecnicoAsignado $tecnicoAsignado)
    {
        //
    }
}
