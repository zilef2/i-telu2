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
        // Expectativas altas
        LosPromps::create(['principal' => "¿Cómo podrías utilizar tus conocimientos y habilidades previas para abordar este nuevo desafío? ¿Qué estrategias usarías para superar cualquier obstáculo?", 'clasificacion' => 'General', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        LosPromps::create(['principal' => "Cree un plan de lección para una clase de estudiantes de [nivel de habilidad del estudiante] que cubra [tema] e incluya una variedad de actividades y evaluaciones.", 'clasificacion' => 'General', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        LosPromps::create(['principal' => "Cree una prueba corta con 5 preguntas de opción múltiple que evalúe la comprensión de los estudiantes sobre [tema]", 'clasificacion' => 'General', 'teoricaOpractica' => 'practica', 'tokensAproximados' => 1]);
        LosPromps::create(['principal' => "Crea una tarea que sea desafiante para los estudiantes que tienen una comprensión sólida de [tema], pero que también brinde apoyo y andamiaje a los estudiantes que tienen dificultades con el material.", 'clasificacion' => 'General', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        LosPromps::create(['principal' => "Cree un póster que enumere las reglas de la clase y explique las consecuencias de romperlas.", 'clasificacion' => 'General', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        LosPromps::create(['principal' => "Genere un ejemplo de un ensayo bien escrito sobre [tema] que cumpla con los criterios de calificación para la maxima nota.", 'clasificacion' => 'General', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        LosPromps::create(['principal' => "Genere una lista de pasos específicos y accionables que un estudiante puede tomar para mejorar su desempeño en [Asignatura].", 'clasificacion' => 'General', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);


        // Enseñanza Explicita
        $promp0 = LosPromps::create(['principal' => "Crea un plan de lección para una unidad sobre [tema] de la Asignatura [Asignatura] que incluya una variedad de actividades y evaluaciones y tenga en cuenta el siguiente párrafo donde brindo una breve descripción de las habilidades y conocimientos de mis alumnos.", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        $promp1 = LosPromps::create(['principal' => "Cree un esquema de lección con intenciones de aprendizaje, actividades creativas y criterios de éxito para una clase sobre [tema] de la Asignatura [Asignatura].", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        $promp4 = LosPromps::create(['principal' => "Genere un glosario de términos y definiciones para una unidad sobre [tema] de la Asignatura [Asignatura].", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        $promp3 = LosPromps::create(['principal' => "Genere un ejemplo de un ensayo bien escrito sobre [tema] en la asigantura [Asignatura] que cumpla con los criterios de evaluación para una calificación de 'A' (arriba), con una anotación detallada que explique los criterios de éxito.", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'teorica', 'tokensAproximados' => 1]);
        $promp2 = LosPromps::create(['principal' => "Genere 5 preguntas de opción múltiple que evalúen la comprensión de los estudiantes sobre [tema] de la Asignatura [Asignatura].", 'clasificacion' => 'Enseñanza Explicita', 'teoricaOpractica' => 'practica', 'tokensAproximados' => 1]);

        $promp0->subtopicos()->attach([ 'subtopico_id' => 1 ]);
        // $promp0->users()->attach([ 'user_id' => 1 ]);
        
        //todo: hasta ahora no se ha usado atar un subtopico a un usuario
        // $promp1->subtopicos()->attach([ 'subtopico_id' => 1 ]); 
        $promp1->users()->attach([ 'user_id' => 1 ]);
        // $promp2->subtopicos()->attach([ 'subtopico_id' => 2 ]); 
        $promp2->users()->attach([ 'user_id' => 2 ]);
        // $promp3->subtopicos()->attach([ 'subtopico_id' => 2 ]); 
        $promp3->users()->attach([ 'user_id' => 3 ]);
        // $promp4->subtopicos()->attach([ 'subtopico_id' => 2 ]); 
        $promp4->users()->attach([ 'user_id' => 4 ]);
        //muchos a muchos con subtopico
        // $response = $this->Universidad->users()->attach( $user );


    }
}
