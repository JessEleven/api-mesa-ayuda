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
                'color_estado'=> '#a3a3a3', //gris
                'orden_prioridad'=> 1
            ],
            [
                'nombre_estado'=> 'En progreso',
                'color_estado'=> '#3b82f6', //azul
                'orden_prioridad'=> 2
            ],
            [
                'nombre_estado'=> 'En espera',
                'color_estado'=> '#facc15', //amariilo
                'orden_prioridad'=> 3
            ],
            [
                'nombre_estado'=> 'Completado',
                'color_estado'=> '#10b981', //verde
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
