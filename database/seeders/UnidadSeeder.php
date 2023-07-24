<?php

namespace Database\Seeders;

use App\Models\Unidad;
use Illuminate\Database\Seeder;

class UnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //unidad            Unidad::create(['nombre'=> 'Trabajo y Energia', 'descripcion'=> 'generico', 'materia_id'=> 1 ]);
        Unidad::create(['nombre' => 'Limites y continuidad de funciones', 'descripcion' => '', 'materia_id' => 2,'enum' => 1,'codigo' => 1]);
        Unidad::create(['nombre' => 'Derivadas implicitas', 'descripcion' => '', 'materia_id' => 2,'enum' => 2,'codigo' => 2]);
        Unidad::create(['nombre' => 'Regla de la cadena', 'descripcion' => '', 'materia_id' => 2,'enum' => 3,'codigo' => 3]);
        Unidad::create(['nombre' => 'Aplicaciones de la derivada', 'descripcion' => '', 'materia_id' => 2,'enum' => 4,'codigo' => 4]);
        Unidad::create(['nombre' => 'Regla de L Hopital', 'descripcion' => '', 'materia_id' => 2,'enum' => 5,'codigo' => 5]);
    }
}
