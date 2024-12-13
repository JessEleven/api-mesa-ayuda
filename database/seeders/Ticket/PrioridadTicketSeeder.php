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
                'color_prioridad'=> '#FF0000', //rojo
                'orden_prioridad'=> 1
            ],
            [
                'nombre_prioridad'=> 'Medio',
                'color_prioridad'=> '#FFA500', //naranja
                'orden_prioridad'=> 2
            ],
            [
                'nombre_prioridad'=> 'Bajo',
                'color_prioridad'=> '#FFFF00', //amarillo
                'orden_prioridad'=> 3
            ],
            [
                'nombre_prioridad'=> 'En espera',
                'color_prioridad'=> '#0000FF', //azul
                'orden_prioridad'=> 4
            ],
            [
                'nombre_prioridad'=> 'Trabajo extra',
                'color_prioridad'=> '#8F8F8F', //gris
                'orden_prioridad'=> 5
            ]
        ];

        foreach ($prioridadTickets as $item) {
            // Se verifica que las prioridades no existan antes de insertarlas
            // para evitar datos duplicados
            PrioridadTicket::firstOrCreate($item);
        }

    }
}
