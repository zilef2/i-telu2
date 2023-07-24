<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
class DatabaseSeeder extends Seeder {
    public function run() {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
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
        ]);
    }
}
