<?php

namespace Database\Seeders;

use App\Models\CentroTrabajo;
use Illuminate\Database\Seeder;

class CentroTrabajoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CentroTrabajo::create([ 'nombre' => 'centro'.rand(10,10000), ]);
        CentroTrabajo::create([ 'nombre' => 'centro'.rand(10001,20000), ]);
        CentroTrabajo::create([ 'nombre' => 'centro'.rand(20001,30000), ]);
    }
}