<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run() {
        // \App\Models\User::factory(10)->create();
        $this->call([
            ParametroSeeder::class,
            
            PermissionSeeder::class,
            RoleSeeder::class,
<<<<<<< HEAD
            
            UserSeeder::class,
            UniversidadSeeder::class,
            CarreraSeeder::class,
            MateriaSeeder::class,
            ObjetivoSeeder::class,
            UnidadSeeder::class,
            SubtopicoSeeder::class,
            LosPrompsSeeder::class,
            EjercicioSeeder::class,
        ]);
=======
            UserSeeder::class,

        ]);
        // Ejercicio::create(['nombre' => 'la funcion x^2 es continua? en que region?', 'descripcion' => 'descripcion generica', 'subtopico_id' => 2]);
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
    }
}
