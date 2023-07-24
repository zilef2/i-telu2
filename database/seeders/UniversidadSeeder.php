<?php

namespace Database\Seeders;

use App\Models\Universidad;
use Illuminate\Database\Seeder;

class UniversidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Universidad::create(['nombre' => 'Universidad Nacional', 'enum' => 1,'codigo'=>1]);
        Universidad::create(['nombre' => 'Universidad de Antioquia', 'enum' => 2,'codigo'=>2]);
        Universidad::create(['nombre' => 'Universidad Pontificia Bolivariana', 'enum' => 3,'codigo'=>3]);
    }
}
