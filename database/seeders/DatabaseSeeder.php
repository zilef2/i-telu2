<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Carrera;
use App\Models\Ejercicio;
use App\Models\Materia;
use App\Models\Objetivo;
use App\Models\Subtopico;
use App\Models\Unidad;
use App\Models\Universidad;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            ParametroSeeder::class,

            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,

        ]);
        // Ejercicio::create(['nombre' => 'la funcion x^2 es continua? en que region?', 'descripcion' => 'descripcion generica', 'subtopico_id' => 2]);
    }
}
