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

        $superadmin = User::create([
            'name'              => 'Superadmin',
            'email'             => 'superadmin@superadmin.com',
            'password'          => bcrypt('superadmin0+-*/'),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'identificacion' => '11232454',

        ]);
        $superadmin->assignRole('superadmin');

        $admin = User::create([
            'name'              => 'Admin',
            'email'             => 'admin@admin.com',
            'password'          => bcrypt('BASEalejoasd00+*??'),
            'email_verified_at' => date('Y-m-d H:i'),
            'sexo' => $sexos[rand(0, 1)],
            'identificacion' => '11232411',
        ]);
        $admin->assignRole('admin');

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
            'madrid' => '1152194567',
            'felizzola' => '1152194568',
            'marta' => '1152194569',
            'liliana' => '1152194510',
            'mabel' => '1152194511',
            'jorge' => '222',
            'emerson' => '333',
        ];

        foreach ($nombresGenericos as $key => $value) {
            $yearRandom = (rand(15, 39));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $unUsuario = User::create([
                'name'              => $key,
                'email'             => $key . '@' . $key . '.com',
                'password'          => bcrypt('asd+-*'),
                'email_verified_at' => date('Y-m-d H:i'),
                'fecha_nacimiento' => $anios,
                'sexo' => $sexos[rand(0, 1)],
                'identificacion' => $value,
            ]);
            $unUsuario->assignRole('trabajador');
        }
    }
}
