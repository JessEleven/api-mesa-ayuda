<?php

namespace Database\Seeders\Ticket;

use App\Models\EstadoTicket;
use Illuminate\Database\Seeder;

class EstadoTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estadoTickets = [
            [
                'nombre_estado'=> 'Nuevo',
                'color_estado'=> '#FFA500', //naranja
                'orden_prioridad'=> 1
            ],
            [
                'nombre_estado'=> 'En progreso',
                'color_estado'=> '#0000FF', //azul
                'orden_prioridad'=> 2
            ],
            [
                'nombre_estado'=> 'En espera',
                'color_estado'=> '#FFFF00', //amariilo
                'orden_prioridad'=> 3
            ],
            [
                'nombre_estado'=> 'Completado',
                'color_estado'=> '#008000', //verde
                'orden_prioridad'=> 4
            ]
        ];

        foreach ($estadoTickets as $item) {
            // Se verifica que las estados no existan antes de insertarlas
            // para evitar datos duplicados
            EstadoTicket::firstOrCreate($item);
        }
    }
}
