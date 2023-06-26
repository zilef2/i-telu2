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
            PosicionUserSeeder::class,
            UserSeeder::class,
            ParametroSeeder::class,
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

        Carrera::create([
            'nombre'=> 'Medicina',
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
        Materia::create([
            'nombre'=> 'Microbiologia',
            'descripcion'=> '',
            'carrera_id'=> 3,
        ]);
        //Tema
        Tema::create(['nombre'=> 'Trabajo y Energia', 'descripcion'=> 'generico', 'materia_id'=> 1 ]);
        Tema::create(['nombre'=> 'celula', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
        //Subtopico
        Subtopico::create([ 'nombre'=> 'Energia cinetica', 'descripcion'=> 'generico', 'tema_id'=> 1 ]);
        Subtopico::create([ 'nombre'=> 'partes importantes de la celula', 'descripcion'=> 'generico', 'tema_id'=> 2 ]);
        //ejercicio
        Ejercicio::create([ 'nombre'=> 'calcular la energia potencial de un cuerpo con masa 1 kg a una altura de 2 metros', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 1 ]);
        Ejercicio::create([ 'nombre'=> 'describa la mitocondria', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 2 ]);
        //Objetivo
        Objetivo::create([ 'nombre'=> 'Aprender fisica general', 'descripcion'=> 'generico', 'resultado_aprendizaje'=> 'Test para mecanica', 'materia_id'=> 1 ]);
        Objetivo::create([ 'nombre'=> 'Tener las bases para entender los microorganismos', 'descripcion'=> 'generico', 'resultado_aprendizaje'=> 'Test de la microbiota', 'materia_id'=> 3 ]);
    }
}
