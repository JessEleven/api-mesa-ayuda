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
                'peso_prioridad'=> '1',
            ],
            [
                'nombre_area'=> 'Obras Públicas',
                'peso_prioridad'=> '2',
            ],
            [
                'nombre_area'=> 'Desarrollo Social',
                'peso_prioridad'=> '3',
            ],
            [
                'nombre_area'=> 'Seguridad Ciudadana',
                'peso_prioridad'=> '4',
            ],
            [
                'nombre_area'=> 'Medio Ambiente',
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
