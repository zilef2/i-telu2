<?php

namespace Database\Seeders;

use App\Models\Ejercicio;
use Illuminate\Database\Seeder;

class EjercicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Ejercicio::create(['nombre' => 'la funcion x^2 es continua? en que region?', 'descripcion' => 'descripcion generica', 'subtopico_id' => 2]);
    }
}
