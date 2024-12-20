<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Organizacion\AreaSeeder;
use Database\Seeders\Organizacion\DepartamentoSeeder;
use Database\Seeders\Ticket\CategoriaTicketSeeder;
use Database\Seeders\Ticket\EstadoTicketSeeder;
use Database\Seeders\Ticket\PrioridadTicketSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            //Relacionado con la organización
            AreaSeeder::class,
            DepartamentoSeeder::class,

            //Relacionado con tickets
            CategoriaTicketSeeder::class,
            EstadoTicketSeeder::class,
            PrioridadTicketSeeder::class
        ]);
    }
}
