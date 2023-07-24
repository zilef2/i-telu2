<?php

namespace Database\Seeders;

use App\Models\Objetivo;
use Illuminate\Database\Seeder;

class ObjetivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Objetivo
        Objetivo::create(['nombre' => 'Entender el movimiento y comportamiento del mundo con los conceptos de la fisica clasica', 'descripcion' => '', 'materia_id' => 1]);
        Objetivo::create(['nombre' => 'Aprender las bases del calculo diferencial y sus aplicaciones', 'descripcion' => '', 'materia_id' => 2]);
    }
}
