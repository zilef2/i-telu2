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
            "Fecha_creacion_parametro" => "2023-07-16",
            "nombre" => "Creacion de la base",
        ]);
    }
}
