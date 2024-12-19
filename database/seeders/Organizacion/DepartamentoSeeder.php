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
        //NOTA: La sigla del departamento debe ser única a nivel global,
        // es decir que no toma en cuenta el área en la que estara

        $departamentos = [
            //Área Finanzas
            [
                'nombre_departamento'=> 'Presupuesto',
                'sigla_departamento'=> 'P',
                'secuencia_departamento'=> '001-P',
                'peso_prioridad'=> '1',
                'id_area'=> '1'
            ],
            [
                'nombre_departamento'=> 'Recaudación',
                'sigla_departamento'=> 'R',
                'secuencia_departamento'=> '002-R',
                'peso_prioridad'=> '2',
                'id_area'=> '1'
            ],
            [
                'nombre_departamento'=> 'Contabilidad',
                'sigla_departamento'=> 'C',
                'secuencia_departamento'=> '003-R',
                'peso_prioridad'=> '3',
                'id_area'=> '1'
            ],

            //Área Obras Públicas
            [
                'nombre_departamento'=> 'Planificación Urbana',
                'sigla_departamento'=> 'PU',
                'secuencia_departamento'=> '001-PU',
                'peso_prioridad'=> '1',
                'id_area'=> '2'
            ],
            [
                'nombre_departamento'=> 'Mantenimiento Vial',
                'sigla_departamento'=> 'MV',
                'secuencia_departamento'=> '002-MV',
                'peso_prioridad'=> '2',
                'id_area'=> '2'
            ],
            [
                'nombre_departamento'=> 'Construcción y Obras',
                'sigla_departamento'=> 'CO',
                'secuencia_departamento'=> '003-CO',
                'peso_prioridad'=> '3',
                'id_area'=> '2'
            ],

            //Área Desarrollo Social
            [
                'nombre_departamento'=> 'Salud Comunitaria',
                'sigla_departamento'=> 'SC',
                'secuencia_departamento'=> '001-SC',
                'peso_prioridad'=> '1',
                'id_area'=> '3'
            ],
            [
                'nombre_departamento'=> 'Educación y Cultura',
                'sigla_departamento'=> 'EC',
                'secuencia_departamento'=> '002-EC',
                'peso_prioridad'=> '2',
                'id_area'=> '3'
            ],
            [
                'nombre_departamento'=> 'Vivienda Social',
                'sigla_departamento'=> 'VS',
                'secuencia_departamento'=> '003-VS',
                'peso_prioridad'=> '3',
                'id_area'=> '3'
            ],

            //Área Seguridad Ciudadana
            [
                'nombre_departamento'=> 'Gestión de Riesgos',
                'sigla_departamento'=> 'GR',
                'secuencia_departamento'=> '001-GR',
                'peso_prioridad'=> '1',
                'id_area'=> '4'
            ],
            [
                'nombre_departamento'=> 'Policía Municipal',
                'sigla_departamento'=> 'PM',
                'secuencia_departamento'=> '002-PM',
                'peso_prioridad'=> '2',
                'id_area'=> '4'
            ],
            [
                'nombre_departamento'=> 'Tránsito y Transporte',
                'sigla_departamento'=> 'TT',
                'secuencia_departamento'=> '003-TT',
                'peso_prioridad'=> '3',
                'id_area'=> '4'
            ],

            //Área Medio Ambiente
            [
                'nombre_departamento'=> 'Gestión de Residuos Sólidos',
                'sigla_departamento'=> 'GRS',
                'secuencia_departamento'=> '001-GRS',
                'peso_prioridad'=> '1',
                'id_area'=> '5'
            ],
            [
                'nombre_departamento'=> 'Educación Ambiental',
                'sigla_departamento'=> 'EA',
                'secuencia_departamento'=> '002-EA',
                'peso_prioridad'=> '2',
                'id_area'=> '5'
            ],
            [
                'nombre_departamento'=> 'Protección de Recursos Naturales',
                'sigla_departamento'=> 'PRN',
                'secuencia_departamento'=> '003-PRN',
                'peso_prioridad'=> '3',
                'id_area'=> '5'
            ],
        ];

        foreach ($departamentos as $item) {
            // Se verifica que los departamentos no existan antes de insertarlas
            // para evitar datos duplicados
            Departamento::firstOrCreate($item);
        }
    }
}
