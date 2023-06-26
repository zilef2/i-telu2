<?php

namespace Database\Seeders;

use App\Models\Parametro;
use Illuminate\Database\Seeder;

class ParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Parametro::create([
            'prompEjercicios'    => 'aun no',//todo: 
            'prompObjetivos'    => 'Eres un rector de universidad veterano. Genera 3 ideas para el objetivo de la asignatura: ',//necesita nombre de la materia
            'NumeroTicketDefecto'=> 4, //ticket: numero promedio de preguntas que puede realizarle a la IA
        ]);
    }
}
