<?php

namespace Database\Seeders;

use App\Models\Articulo;
use Illuminate\Database\Seeder;

class ArticuloSeeder extends Seeder {
    public function run() {
        Articulo::create([
            'nick' => 'random nick',
            'version' => 'random version',
            'Portada' => 'Portada asd0',
            'Resumen' => 'Resumen asd1',
            'Palabras_Clave' => 'una Palabras_Clave asd2',
            'Introduccion' => 'Introduccion asd3',
            'Revision_de_la_Literatura' => 'Revisión_de_la_Literatura asd4',
            'Metodologia' => 'Metodologia asd5',
            'Resultados' => 'Resultados asd6',
            'Discusion' => 'Discusion asd7',
            'Conclusiones' => 'Conclusiones asd8',
            'Agradecimientos' => 'Agradecimientos asd9',
            'Referencias' => 'Referencias asd10',
            'Anexos_o_Apendices' => 'Anexos_o_Apendices asd11',
            'user_id' => 2,
            'universidad_id' => 1,
            'carrera_id' => 1,
            'materia_id' => 1,
            'Resumen_critica' => 3,
            'Introduccion_critica' => 3,
            'Discusion_critica' => 3,
            'Conclusiones_critica' => 3,
            'Metodologia_critica' => 3,

        ]);
    }
}
