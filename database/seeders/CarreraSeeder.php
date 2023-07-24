<?php

namespace Database\Seeders;

use App\Models\Carrera;
use Illuminate\Database\Seeder;

class CarreraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Carrera y materia
        Carrera::create([
            'nombre' => 'Fisica Pura',
            'descripcion' => '',
            'universidad_id' => 1,
            'enum' => 1,
            'codigo' => 1,
        ]);
        Carrera::create([
            'nombre' => 'Quimica',
            'descripcion' => '',
            'universidad_id' => 1,
            'enum' => 2,
            'codigo' => 2,
        ]);
        Carrera::create([
            'nombre' => 'Artes plasticas',
            'descripcion' => '',
            'universidad_id' => 1,
            'enum' => 3,
            'codigo' => 3,
        ]);

        Carrera::create([
            'nombre' => 'Medicina',
            'descripcion' => '',
            'universidad_id' => 2,
            'enum' => 4,
            'codigo' => 4,
        ]);
        Carrera::create([
            'nombre' => 'Arquitectura',
            'descripcion' => '',
            'universidad_id' => 2,
            'enum' => 5,
            'codigo' => 5,
        ]);
    }
}
