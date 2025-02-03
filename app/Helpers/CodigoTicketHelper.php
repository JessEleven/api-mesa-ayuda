<?php

namespace App\Helpers;

use App\Models\Ticket;
use App\Models\Usuario;
use App\Models\CategoriaTicket;

class CodigoTicketHelper
{
    public static function generateCodigoTicket($idUsuario, $idCategoria)
    {
        $user = Usuario::findOrFail($idUsuario);
        $category = CategoriaTicket::findOrFail($idCategoria);

        // Se obtiene laz siglas del departamento y de la categoría
        $siglaD = $user->departamentos->sigla_departamento;
        $siglaC = $category->sigla_categoria;

        // Se ontiene el número del siguiente ticket para este usuario (sin considerar las siglas)
        $lastTicket = Ticket::where("id_usuario", $idUsuario)
            ->orderBy("id", "desc")
            ->value("codigo_ticket");

        // Se extrae el número de ticket, si existe (sin considerar las siglas)
        $lastNumber = $lastTicket ? (int) substr($lastTicket, -6) : 0;
        $nextNumber = str_pad($lastNumber + 1, 6, "0", STR_PAD_LEFT);

        return $siglaD . '-' . $siglaC . '-' . $nextNumber;
    }
}
