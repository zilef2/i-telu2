<?php

namespace Database\Seeders;

use App\Models\Subtopico;
use Illuminate\Database\Seeder;

class SubtopicoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Subtopico::create(['nombre' => 'Definicion de un limite', 'descripcion' => '', 'unidad_id' => 1, 'resultado_aprendizaje' => 'Aprender Definiciones','enum' => 1,'codigo' => 1]);
        Subtopico::create(['nombre' => 'Continuidad de funciones', 'descripcion' => '', 'unidad_id' => 1, 'resultado_aprendizaje' => 'Sabe cuando una funcion es continua o discontinua','enum' => 2,'codigo' => 2]);
    }
}
