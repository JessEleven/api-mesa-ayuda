<?php

namespace Database\Seeders\Ticket;

use App\Models\PrioridadTicket;
use Illuminate\Database\Seeder;

class PrioridadTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $prioridadTickets = [
            [
                'nombre_prioridad'=> 'Alta',
                'color_prioridad'=> '#ef4444', //rojo
                'orden_prioridad'=> 1
            ],
            [
                'nombre_prioridad'=> 'Media',
                'color_prioridad'=> '#fb923c', //naranja
                'orden_prioridad'=> 2
            ],
            [
                'nombre_prioridad'=> 'Baja',
                'color_prioridad'=> '#facc15', //amarillo
                'orden_prioridad'=> 3
            ]
        ];

        foreach ($prioridadTickets as $item) {
            // Se verifica que las prioridades no existan antes de insertarlas
            // para evitar datos duplicados
            PrioridadTicket::firstOrCreate($item);
        }

    }
}
