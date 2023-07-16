<?php

namespace Database\Seeders;

use App\Models\LosPromps;
use Illuminate\Database\Seeder;

class LosPrompsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Expectativas Altas
        LosPromps::create(['principal' => "¿Cómo podrías utilizar tus conocimientos y habilidades previas para abordar este nuevo desafío? ¿Qué estrategias usarías para superar cualquier obstáculo?", 'clasificacion' => 'Expectativas Altas', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Cree un plan de lección para una clase de trabajadors de [nivel de habilidad del trabajador] que cubra [tema] e incluya una variedad de actividades y evaluaciones.", 'clasificacion' => 'Expectativas Altas', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Cree una prueba corta con 5 preguntas de opción múltiple que evalúe la comprensión de los trabajadors sobre [tema]", 'clasificacion' => 'Expectativas Altas', 'teoricaOpractica' => 'practica']);
        LosPromps::create(['principal' => "Crea una tarea que sea desafiante para los trabajadors que tienen una comprensión sólida de [tema], pero que también brinde apoyo y andamiaje a los trabajadors que tienen dificultades con el material.", 'clasificacion' => 'Expectativas Altas', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Cree un póster que enumere las reglas de la clase y explique las consecuencias de romperlas.", 'clasificacion' => 'Expectativas Altas', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Genere un ejemplo de un ensayo bien escrito sobre [tema] que cumpla con los criterios de calificación para la maxima nota.", 'clasificacion' => 'Expectativas Altas', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Genere una lista de pasos específicos y accionables que un trabajador puede tomar para mejorar su desempeño en [asignatura].", 'clasificacion' => 'Expectativas Altas', 'teoricaOpractica' => 'teorica']);


        // Enseñanza Explicita
        LosPromps::create(['principal' => "Crea un plan de lección para una unidad sobre [concepto que se enseña] que incluya una variedad de actividades y evaluaciones y tenga en cuenta el siguiente párrafo donde brindo una breve descripción de las habilidades y conocimientos de mis alumnos.", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Cree un esquema de lección con intenciones de aprendizaje, actividades creativas y criterios de éxito para una clase sobre [concepto que se enseña].", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Genere 5 preguntas de opción múltiple que evalúen la comprensión de los trabajadors sobre [el concepto que se enseña].", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'practica']);
        LosPromps::create(['principal' => "Genere un ejemplo de un ensayo bien escrito sobre [tema] que cumpla con los criterios de evaluación para una calificación de 'A' (arriba), con una anotación detallada que explique los criterios de éxito.", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica']);
        LosPromps::create(['principal' => "Genere un glosario de términos y definiciones para una unidad sobre [concepto que se enseña].", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica']);
    }
}
