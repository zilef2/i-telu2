<?php

namespace Database\Seeders;

use App\Models\Materia;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Materia
        Materia::create([
            'nombre' => 'Fisica mecanica',
            'descripcion' => '',
            'carrera_id' => 1,
            'enum' => 1,
            'codigo' => 1,
        ]);
        Materia::create([
            'nombre' => 'Calculo diferencial',
            'descripcion' => ' rama del análisis matemático que se enfoca en el estudio de las tasas de cambio y la derivación de funciones. Permite analizar la variación instantánea de una función y aplicar conceptos como límites, derivadas e infinitesimales para resolver problemas de optimización, modelado y predicción.',
            'carrera_id' => 1,
            'enum' => 2,
            'codigo' => 2,
        ]);
        Materia::create([
            'nombre' => 'Microbiologia',
            'descripcion' => '',
            'carrera_id' => 3,
            'enum' => 3,
            'codigo' => 3,
        ]);
    }
}
