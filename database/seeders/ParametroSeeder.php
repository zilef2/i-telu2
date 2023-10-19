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
            'id' => 1,
            'prompEjercicios'       => "Eres un experto en la asignatura (asignatura), responda el siguiente ejercicio: (pregunta), para un estudiante de nivel (nivel). Antes de resolver la pregunta, genera un contexto, si es posible, de entre 20 y 40 palabras. Cuando finalices el contexto, deja un renglon vacio . Al finalizar la respuesta, sugiere 3 ejercicios para preguntarle a una inteligencia artificial(ponle de titulo (plantillaPracticar)) y seguir aprendiendo de (asignatura)",
            'prompObjetivos'        => 'Eres un rector de universidad experimentado. Genera 3 ideas para el objetivo de la asignatura: ', //necesita nombre de la materia
            'pMejoraContinua'       => " Estoy tratando de mejorar mi compresion en [Unidad]. Hazme una pregunta algebraica y sigue haciéndome preguntas adaptativas para que yo mejore. Si obtengo la respuesta correcta, dame una pregunta más difícil, si respondo mal, proporciona retroalimentación y dame una pregunta más fácil. ",
            'prompExplicarTema'     => "Eres un experto en la asignatura de (asignatura), explica el tema:(tema) de la unidad (Unidad) para un estudiante de nivel (nivel).",
            
            'NumeroTicketDefecto'   => 15, //ticket: numero promedio de preguntas que puede realizarle a la IA
        ]);
        Parametro::create([
            'id' => 2,
            'prompEjercicios'       => "Por favor, mejore la argumentación, coherencia y unidad del siguiente texto. Evitando las posibles redundancias. ",
            'prompObjetivos'        => "Califique el siguiente articulo universitario, siendo 0 la nota mas baja y 5 la mas alta (un trabajo perfecto). ",
            'pMejoraContinua'       => "",
            'prompExplicarTema'     => "",
            
            'NumeroTicketDefecto'   => 0, 
        ]);
        Parametro::create([
            'id' => 3,
            'prompEjercicios'       => "100",
            'prompObjetivos'        => "200",
            'pMejoraContinua'       => "300",
            'prompExplicarTema'     => "0",
            
            'NumeroTicketDefecto'   => 0, 
        ]);
    }
}
