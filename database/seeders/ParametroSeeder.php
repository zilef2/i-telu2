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
            'prompEjercicios'    => "Eres un experto en la asignatura (materia_nombre), responda el siguiente ejercicio: (pregunta), para un estudiante de nivel (nivel). Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras. Cuando finalices el contexto, deja un renglon vacio . Al finalizar la respuesta, sugiere 3 ejercicios para preguntarle a una inteligencia artificial(ponle de titulo (plantillaPracticar)) y seguir aprendiendo de (materia_nombre)",

            'prompObjetivos'    => 'Eres un rector de universidad experimentado. Genera 3 ideas para el objetivo de la asignatura: ', //necesita nombre de la materia

            'pMejoraContinua'    => " Estoy tratando de mejorar mi compresion en [Unidad]. Hazme una pregunta algebraica y sigue haciéndome preguntas adaptativas para que yo mejore. Si obtengo la respuesta correcta, dame una pregunta más difícil, si respondo mal, proporciona retroalimentación y dame una pregunta más fácil. ",


            'prompExplicarTema'    => "Eres un experto en la asignatura de (materia_nombre), explica el Unidad (Unidad) para estudiante de nivel (nivel).",

            'NumeroTicketDefecto' => 5, //ticket: numero promedio de preguntas que puede realizarle a la IA
        ]);
    }
}
