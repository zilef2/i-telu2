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
        //ejercicio
        // Ejercicio::create(['nombre' => 'calcular la energia potencial de un cuerpo con masa 1 kg a una altura de 2 metros', 'descripcion' => 'descripcion generica', 'subtopico_id' => 1]);
        // Ejercicio::create(['nombre' => 'calcular la energia cinetica de un cuerpo con masa 100 kg a una altura de 0.02 metros, justo cuando toca el suelo', 'descripcion' => 'descripcion generica', 'subtopico_id' => 1]);
        Ejercicio::create(['nombre' => 'la funcion x^2 es continua? en que region?', 'descripcion' => 'descripcion generica', 'subtopico_id' => 2]);
    }
}
