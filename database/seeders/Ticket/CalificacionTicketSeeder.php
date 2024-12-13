<?php

namespace Database\Seeders\Ticket;

use App\Models\CalificacionTicket;
use Illuminate\Database\Seeder;

class CalificacionTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $calificacionTicket = [
            [
                'calificacion'=> '10',
                'observacion'=> ''
            ],
            [
                'calificacion'=> '6',
                'observacion'=> 'Falto una revisión más a profundiad del sistema'
            ],
            [
                'calificacion'=> '8',
                'observacion'=> 'Exelente el nuevo adaptador para el cable HMDI'
            ]
        ];

        foreach ($calificacionTicket as $item) {
            // Se verifica que las calificaciones no existan antes de insertarlas
            // para evitar datos duplicados
            CalificacionTicket::firstOrCreate($item);
        }
    }
}
