<?php

namespace Database\Seeders\Organizacion;

use App\Models\Area;
use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $areas = [
            [
                'nombre_area'=> 'Finanzas',
                'sigla_area'=> 'F',
                'secuencia_area'=> '001-F',
                'peso_prioridad'=> '1',
            ],
            [
                'nombre_area'=> 'Obras Públicas',
                'sigla_area'=> 'OP',
                'secuencia_area'=> '002-OP',
                'peso_prioridad'=> '2',
            ],
            [
                'nombre_area'=> 'Desarrollo Social',
                'sigla_area'=> 'DS',
                'secuencia_area'=> '003-DS',
                'peso_prioridad'=> '3',
            ],
            [
                'nombre_area'=> 'Seguridad Ciudadana',
                'sigla_area'=> 'SC',
                'secuencia_area'=> '004-SC',
                'peso_prioridad'=> '4',
            ],
            [
                'nombre_area'=> 'Medio Ambiente',
                'sigla_area'=> 'MA',
                'secuencia_area'=> '005-MA',
                'peso_prioridad'=> '5',
            ]
        ];

        foreach ($areas as $item) {
            // Se verifica que las areas no existan antes de insertarlas
            // para evitar datos duplicados
            Area::firstOrCreate($item);
        }
    }
}
