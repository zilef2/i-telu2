<?php

namespace Database\Seeders;

use App\Models\PosicionUser;
use Illuminate\Database\Seeder;

class PosicionUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PosicionUser::create(['nombre' => 'sinPosicion', 'importancia' => 1]); //1 es la menos importante
        // PosicionUser::create([ 'nombre'=> 'trabajador', 'importancia'=> 2 ]);//1 es la menos importante
        // PosicionUser::create([ 'nombre'=> 'profesor', 'importancia'=> 3 ]);
        // PosicionUser::create([ 'nombre'=> 'coordinador de programa', 'importancia'=> 4 ]);
        // PosicionUser::create([ 'nombre'=> 'coordinador de programa', 'importancia'=> 5 ]);

    }
}
