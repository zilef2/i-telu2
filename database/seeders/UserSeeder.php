<?php

namespace Database\Seeders;

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
        $sexos = ['Masculino', 'Femenino'];
        $genPa = env('sap_gen');

        $superadmin = User::create([
            'name'              => 'Super',
            'email'             => 'ajelof2@gmail.com',
            'password'          => bcrypt($genPa.'super+-*99'),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'identificacion' => '11232454',
        ]); $superadmin->assignRole('superadmin');

        $admin = User::create([
            'name'              => 'Admin',
            'email'             => 'admin@admin.com',
<<<<<<< HEAD
            'password'          => bcrypt($genPa.'+-*'),
=======
            'password'          => bcrypt('BASEalejoasd00+*??'),
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'identificacion' => '11232411',
        ]);
        $admin->assignRole('admin');

<<<<<<< HEAD
        $nombresGenericos= [ 
            'estudiante' => '1231567899',
            'alejo' => '1232567899',
            'jose' =>  '1152194566',
=======
        $trabajador = User::create([
            'name'              => 'trabajador',
            'email'             => 'trabajador@trabajador.com',
            'password'          => bcrypt('trabajador00+*'),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'identificacion' => '11232412',
        ]);
        $trabajador->assignRole('trabajador');

        $nombresGenericos = [
            'jose' => '1152888999',
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
            'madrid' => '1152194567',
            'felizzola' => '1152194568',
            'marta' => '1152194569',
            'liliana' => '1152194510',
            'mabel' => '1152194511',
<<<<<<< HEAD
            'miguel' => '1152194512',
            'jorge' => '1152194513',
            'emerson' => '1152194514',
            'amaya' => '11521222514',
=======
            'jorge' => '222',
            'emerson' => '333',
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
        ];

        foreach ($nombresGenericos as $key => $value) {
            $yearRandom = (rand(15, 39));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name'              => $key,
<<<<<<< HEAD
                'email'             => $key.'@'.$key.'.com',
                'password'          => bcrypt($genPa.'asd+-*'),
=======
                'email'             => $key . '@' . $key . '.com',
                'password'          => bcrypt('asd+-*'),
>>>>>>> a3a47f4b68ef3f01c9a880a3ed85bb7aff8eb3ae
                'email_verified_at' => date('Y-m-d H:i'),
                'fecha_nacimiento' => $anios,
                'sexo' => $sexos[rand(0, 1)],
                'identificacion' => $value,
            ]);
            $unUsuario->assignRole('trabajador');
        }
    }
}
