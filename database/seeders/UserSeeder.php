<?php

namespace Database\Seeders;

use App\Models\Parametro;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genPa = env('sap_gen');

        
        $sexos = ['Masculino', 'Femenino'];
        $grados = ['bachiller','pregrado','postgrado'];
        $parametro = Parametro::first();
        $NumTickesDefecto = $parametro->NumeroTicketDefecto;

        $superadmin = User::create([
            'name'              => 'Super',
            'email'             => 'ajelof2@gmail.com',
            'password'          => bcrypt($genPa.'super0.+-*'.$genPa),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'identificacion' => '11232454',
            'limite_token_general' => $NumTickesDefecto,
                'limite_token_leccion' => $NumTickesDefecto,
        ]); $superadmin->assignRole('superadmin');

        $nombreAdmin = 'Admin';
        $App = env('APP_NAME'); //AdminIntelU@gmail.com
        $admin = User::create([
            'name'              => "$nombreAdmin $App",
            'email'             => "$nombreAdmin$App"."@gmail.com",
            'password'          => bcrypt($genPa.'0.+-*'.$genPa),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'identificacion' => '11232411',
            'limite_token_general' => $NumTickesDefecto,
            'limite_token_leccion' => $NumTickesDefecto,
        ]);
        $admin->assignRole('admin');

        $nombresGenericos= [ 
            'estudiante' => '1231567899',
            'alejo' => '1232567899',
            'jose' =>  '1152194566',
            'madrid' => '1152194567',
            'liliana' => '1152194510',
            'miguel' => '1152194512',
            'jorge' => '1152194513',
            'emerson' => '1152194514',
            'amaya' => '11521222514',
        ];

        foreach ($nombresGenericos as $key => $value) {
            $yearRandom = (rand(15, 39));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name'              => $key,
                'email'             => $key.'@'.$key.'.com',
                'password'          => bcrypt($genPa.'asd+-*'),
                'email_verified_at' => date('Y-m-d H:i'),
                'fecha_nacimiento' => $anios,
                'sexo' => $sexos[rand(0, 1)],
                'identificacion' => $value,
                'semestre' => rand(1,10),
                'pgrado' => $grados[rand(0,2)],
                'limite_token_general' => $NumTickesDefecto,
                'limite_token_leccion' => $NumTickesDefecto,
            ]);
            $unUsuario->assignRole('estudiante');
        }
    }
}
