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

        //Carrera y materia
            Carrera::create([
                'nombre'=> 'Fisica Pura',
                'descripcion'=> '',
                'universidad_id'=> 1,
            ]);
            Carrera::create([
                'nombre'=> 'Quimica',
                'descripcion'=> '',
                'universidad_id'=> 1,
            ]);
            Carrera::create([
                'nombre'=> 'Artes plasticas',
                'descripcion'=> '',
                'universidad_id'=> 1,
            ]);

            Carrera::create([
                'nombre'=> 'Medicina',
                'descripcion'=> '',
                'universidad_id'=> 2,
            ]);
            Carrera::create([
                'nombre'=> 'Arquitectura',
                'descripcion'=> '',
                'universidad_id'=> 2,
            ]);

            //Materia
            Materia::create([
                'nombre'=> 'Fisica',
                'descripcion'=> '',
                'carrera_id'=> 1,
            ]);
            Materia::create([
                'nombre'=> 'Calculo diferencial',
                'descripcion'=> ' rama del análisis matemático que se enfoca en el estudio de las tasas de cambio y la derivación de funciones. Permite analizar la variación instantánea de una función y aplicar conceptos como límites, derivadas e infinitesimales para resolver problemas de optimización, modelado y predicción.',
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
            Tema::create(['nombre'=> 'Limites y continuidad de funciones', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
            Tema::create(['nombre'=> 'Que es una funcion', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
            Tema::create(['nombre'=> 'Derivadas implicitas', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
            Tema::create(['nombre'=> 'Regla de la cadena', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
            Tema::create(['nombre'=> 'Aplicaciones de la derivada', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
            Tema::create(['nombre'=> 'Regla de L Hopital', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
            Tema::create(['nombre'=> 'Teorema del valor medio y teorema de rolle', 'descripcion'=> 'generico', 'materia_id'=> 2 ]);
        //Subtopico
            Subtopico::create([ 'nombre'=> 'Energia cinetica', 'descripcion'=> 'generico', 'tema_id'=> 1 ]);
            Subtopico::create([ 'nombre'=> 'partes importantes de la celula', 'descripcion'=> 'generico', 'tema_id'=> 2 ]);
            //Objetivo
            Objetivo::create([ 'nombre'=> 'Aprender fisica general', 'descripcion'=> 'generico', 'resultado_aprendizaje'=> 'Test para mecanica', 'materia_id'=> 1 ]);
            Objetivo::create([ 'nombre'=> 'Tener las bases para entender los microorganismos', 'descripcion'=> 'generico', 'resultado_aprendizaje'=> 'Test de la microbiota', 'materia_id'=> 3 ]);
        //ejercicio
            Ejercicio::create([ 'nombre'=> 'calcular la energia potencial de un cuerpo con masa 1 kg a una altura de 2 metros', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 1 ]);
            Ejercicio::create([ 'nombre'=> 'calcular la energia cinetica de un cuerpo con masa 100 kg a una altura de 0.02 metros, justo cuando toca el suelo', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 1 ]);
            Ejercicio::create([ 'nombre'=> 'describa la mitocondria', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 2 ]);
            Ejercicio::create([ 'nombre'=> 'Que otros tipos de molecula se asemejan al ATP', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 2 ]);
            Ejercicio::create([ 'nombre'=> 'Describa el nucleo', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 2 ]);
            Ejercicio::create([ 'nombre'=> 'Describa la pared celular', 'descripcion'=> 'descripcion generica', 'subtopico_id'=> 2 ]);
    }
}
