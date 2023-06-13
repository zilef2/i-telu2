<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Carrera;
use App\Models\Ejercicio;
use App\Models\Materia;
use App\Models\Objetivo;
use App\Models\Subtopico;
use App\Models\Tema;
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

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
        //Universidad
        Universidad::create([ 'nombre'=> 'Universidad Nacional', ]);
        Universidad::create([ 'nombre'=> 'Universidad de Antioquia', ]);
        Universidad::create([ 'nombre'=> 'Universidad Pontificia Bolivariana', ]);

        //Carrera
        Carrera::create([
            'nombre'=> 'Fisica Pura',
            'descripcion'=> 'la mejor',
            'universidad_id'=> 1,
        ]);
        Carrera::create([
            'nombre'=> 'Quimica',
            'descripcion'=> 'Mejor',
            'universidad_id'=> 1,
        ]);

        //Materia
        Materia::create([
            'nombre'=> 'Fisica',
            'descripcion'=> 'Mejor',
            'carrera_id'=> 1,
        ]);
        Materia::create([
            'nombre'=> 'Calculo diferencial',
            'descripcion'=> 'la mejor',
            'carrera_id'=> 1,
        ]);
        //Tema
        Tema::create(['nombre'=> 'Trabajo y Energia', 'descripcion'=> 'generico', 'materia_id'=> 1 ]);
        //Subtopico
        Subtopico::create([ 'nombre'=> 'calculo de la energia cinetica y potencial', 'descripcion'=> 'generico', 'tema_id'=> 1 ]);
        //ejercicio
        Ejercicio::create([ 'nombre'=> 'calcular la energia potencial de un cuerpo con masa 1 kg a una altura de 2 metros', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 1 ]);
        //Objetivo
        Objetivo::create([ 'nombre'=> 'Aprender fisica general', 'descripcion'=> 'descripcion', 'materia_id'=> 1 ]);
    }
}
