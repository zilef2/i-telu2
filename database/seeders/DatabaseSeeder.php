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
            
            UserSeeder::class,
            UniversidadSeeder::class,
            CarreraSeeder::class,
            MateriaSeeder::class,
            ObjetivoSeeder::class,
            UnidadSeeder::class,
            SubtopicoSeeder::class,
            LosPrompsSeeder::class,
            EjercicioSeeder::class,

            //2oct
            ArticuloSeeder::class,
            PlanSeeder::class,
        ]);
    }
}
