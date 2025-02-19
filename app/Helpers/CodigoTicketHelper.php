<?php

namespace App\Helpers;

use App\Models\Ticket;
use App\Models\Usuario;
use App\Models\CategoriaTicket;

// NOTA: Cada vez que se crea un helper se debe registrar el archivo manualmente al composer.json
// en la sección de "autoload", para que se carge automáticamente se ejecuta composer dump-autoload
// Cada vez que crea, modifica o elimina (código o archivo) se debe ejecutar el comando

class CodigoTicketHelper
{
    public static function generateCodigoTicket($userId, $categoryId)
    {
        $user = Usuario::find($userId);
        $category = CategoriaTicket::find($categoryId);

        // Se obtiene laz siglas del departamento y de la categoría
        $siglaD = $user->departamento->sigla_departamento;
        $siglaC = $category->sigla_categoria;

        // Se ontiene el número del siguiente ticket para este usuario (sin considerar las siglas)
        $lastTicket = Ticket::where("id_usuario", $userId)
            ->orderBy("id", "desc")
            ->value("codigo_ticket");

        // Se extrae el número de ticket, si existe (sin considerar las siglas)
        $lastNumber = $lastTicket ? (int) substr($lastTicket, -6) : 0;

        $nextNumber = str_pad($lastNumber + 1, 6, "0", STR_PAD_LEFT);

        $code_ticket = $siglaD . "-" . $siglaC . "-" . $nextNumber;

        return $code_ticket;
    }
}
