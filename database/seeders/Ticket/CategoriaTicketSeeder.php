<?php

namespace Database\Seeders\Ticket;

use App\Models\CategoriaTicket;
use Illuminate\Database\Seeder;

class CategoriaTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriaTickets = [
            [
                'nombre_categoria'=> 'Hardware',
                'sigla_categoria'=> 'HW'
            ],
            [
                'nombre_categoria'=> 'Software',
                'sigla_categoria'=> 'SW'
            ]
        ];

        foreach ($categoriaTickets as $item) {
            // Se verifica que las categorias no existan antes de insertarlas
            // para evitar datos duplicados
            CategoriaTicket::firstOrCreate($item);
        }
    }
}
