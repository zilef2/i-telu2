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

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            LosPrompsSeeder::class,
            ParametroSeeder::class,

            PermissionSeeder::class,
            RoleSeeder::class,

            UserSeeder::class,
        ]);
        //Universidad
        Universidad::create(['nombre' => 'Universidad Nacional',]);
        Universidad::create(['nombre' => 'Universidad de Antioquia',]);
        Universidad::create(['nombre' => 'Universidad Pontificia Bolivariana',]);

        //Carrera y materia
        Carrera::create([
            'nombre' => 'Fisica Pura',
            'descripcion' => '',
            'universidad_id' => 1,
        ]);
        Carrera::create([
            'nombre' => 'Quimica',
            'descripcion' => '',
            'universidad_id' => 1,
        ]);
        Carrera::create([
            'nombre' => 'Artes plasticas',
            'descripcion' => '',
            'universidad_id' => 1,
        ]);

        Carrera::create([
            'nombre' => 'Medicina',
            'descripcion' => '',
            'universidad_id' => 2,
        ]);
        Carrera::create([
            'nombre' => 'Arquitectura',
            'descripcion' => '',
            'universidad_id' => 2,
        ]);

        //Materia
        Materia::create([
            'nombre' => 'Fisica mecanica',
            'descripcion' => '',
            'carrera_id' => 1,
        ]);
        Materia::create([
            'nombre' => 'Calculo diferencial',
            'descripcion' => ' rama del análisis matemático que se enfoca en el estudio de las tasas de cambio y la derivación de funciones. Permite analizar la variación instantánea de una función y aplicar conceptos como límites, derivadas e infinitesimales para resolver problemas de optimización, modelado y predicción.',
            'carrera_id' => 1,
        ]);
        Materia::create([
            'nombre' => 'Microbiologia',
            'descripcion' => '',
            'carrera_id' => 3,
        ]);

        //Objetivo
        Objetivo::create(['nombre' => 'Entender el movimiento y comportamiento del mundo con los conceptos de la fisica clasica', 'descripcion' => '', 'materia_id' => 1]);
        Objetivo::create(['nombre' => 'Aprender las bases del calculo diferencial y sus aplicaciones', 'descripcion' => '', 'materia_id' => 2]);

        //unidad            Unidad::create(['nombre'=> 'Trabajo y Energia', 'descripcion'=> 'generico', 'materia_id'=> 1 ]);
        Unidad::create(['nombre' => 'Limites y continuidad de funciones', 'descripcion' => '', 'materia_id' => 2]);
        Unidad::create(['nombre' => 'Derivadas implicitas', 'descripcion' => '', 'materia_id' => 2]);
        Unidad::create(['nombre' => 'Regla de la cadena', 'descripcion' => '', 'materia_id' => 2]);
        Unidad::create(['nombre' => 'Aplicaciones de la derivada', 'descripcion' => '', 'materia_id' => 2]);
        Unidad::create(['nombre' => 'Regla de L Hopital', 'descripcion' => '', 'materia_id' => 2]);
        //Subtopico
        Subtopico::create(['nombre' => 'Definicion de un limite', 'descripcion' => '', 'unidad_id' => 1, 'resultado_aprendizaje' => 'Aprender Definiciones']);
        Subtopico::create(['nombre' => 'Continuidad de funciones', 'descripcion' => '', 'unidad_id' => 1, 'resultado_aprendizaje' => 'Sabe cuando una funcion es continua o discontinua']);

        
        //ejercicio
        // Ejercicio::create(['nombre' => 'calcular la energia potencial de un cuerpo con masa 1 kg a una altura de 2 metros', 'descripcion' => 'descripcion generica', 'subtopico_id' => 1]);
        // Ejercicio::create(['nombre' => 'calcular la energia cinetica de un cuerpo con masa 100 kg a una altura de 0.02 metros, justo cuando toca el suelo', 'descripcion' => 'descripcion generica', 'subtopico_id' => 1]);
        Ejercicio::create(['nombre' => 'la funcion x^2 es continua? en que region?', 'descripcion' => 'descripcion generica', 'subtopico_id' => 2]);
    }
}
