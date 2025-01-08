<?php

namespace App\Http\Controllers;

use App\Http\Responses\ApiResponse;
use App\Models\BitacoraTicket;
use Exception;
use Illuminate\Http\Request;

class BitacoraTicketController extends Controller
{
    public function index()
    {
        try {
            $allBinnacles = BitacoraTicket::with("tecnico_asigados")
                ->orderBy("id", "asc")
                ->paginate(20);

            if ($allBinnacles->isEmpty()) {
                return ApiResponse::success(
                    "Listado de bitácoras vacía",
                    200,
                    $allBinnacles
                );
            }

            return ApiResponse::success(
                "Listado de bitácoras",
                200,
                $allBinnacles
            );

        } catch (Exception $e) {
            return ApiResponse::error(
                "Ha ocurrido un error inesperado",
                500
            );
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(BitacoraTicket $bitacoraTicket)
    {
        //
    }

    public function update(Request $request, BitacoraTicket $bitacoraTicket)
    {
        //
    }

    public function destroy(BitacoraTicket $bitacoraTicket)
    {
        //
    }
}
