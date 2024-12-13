<?php

namespace Database\Seeders\Organizacion;

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departamentos = [
            [
                'nombre_departamento'=> 'Presupuesto',
                'peso_prioridad'=> '1',
            ],
            [
                'nombre_departamento'=> 'Planificación Urbana',
                'peso_prioridad'=> '2',
            ],
            [
                'nombre_departamento'=> 'Salud Comunitaria',
                'peso_prioridad'=> '3',
            ],
            [
                'nombre_departamento'=> 'Gestión de Riesgos',
                'peso_prioridad'=> '4',
            ],
            [
                'nombre_departamento'=> 'Gestión de Residuos Sólidos',
                'peso_prioridad'=> '5',
            ]
        ];

        foreach ($departamentos as $item) {
            // Se verifica que los departamentos no existan antes de insertarlas
            // para evitar datos duplicados
            Departamento::firstOrCreate($item);
        }
    }
}
