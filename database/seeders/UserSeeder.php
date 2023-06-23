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
        $superadmin = User::create([
            'name'              => 'Superadmin',
            'email'             => 'superadmin@superadmin.com',
            'password'          => bcrypt('superadmin0+-*/'),
            'email_verified_at' => date('Y-m-d H:i'),
            'posicion_id'=>3//coordinador de programa
        ]);
        $superadmin->assignRole('superadmin');

        $admin = User::create([
            'name'              => 'Admin',
            'email'             => 'admin@admin.com',
            'password'          => bcrypt('alejoasd00+*??'),
            'email_verified_at' => date('Y-m-d H:i'),
            'posicion_id'=>3//coordinador de programa
        ]);
        $admin->assignRole('admin');

        $estudiante = User::create([
            'name'              => 'estudiante',
            'email'             => 'estudiante@estudiante.com',
            'password'          => bcrypt('estudiante00+*'),
            'email_verified_at' => date('Y-m-d H:i'),
            'posicion_id' => 1
        ]);
        $estudiante->assignRole('estudiante');
        
        $alejo = User::create([
            'name'              => 'alejo',
            'email'             => 'alejo@alejo.com',
            'password'          => bcrypt('alejo00+*'),
            'email_verified_at' => date('Y-m-d H:i'),
            'posicion_id' => 1
        ]); $alejo->assignRole('estudiante');
        

        
        $nombresGenericos= [ 
            'jose' => '1152194566',
            'madrid' => '1152194567',
            'felizzola' => '1152194568',
            'marta' => '1152194569',
            'liliana' => '1152194510',
            'mabel' => '1152194511',
            'miguel' => '1152194512',
            'jorge' => '1152194513',
            'emerson' => '1152194514',
        ];
        $grados = ['bachiller','pregrado','postgrado'];
        foreach ($nombresGenericos as $key => $value) {
            $yearRandom = (rand(15,39));
            $anios = Carbon::now()->subYears($yearRandom)->format('Y-m-d H:i');
            $sexos = ['Masculino', 'Femenino'];
            $unUsuario = User::create([
                'name'              => $key,
                'email'             => $key.'@'.$key.'.com',
                'password'          => bcrypt('asd+-*/'),
                'email_verified_at' => date('Y-m-d H:i'),
                'fecha_nacimiento' => $anios,
                'sexo' => $sexos[rand(0,1)],
                'posicion_id' => 1,
                'identificacion' => $value,
                'semestre' => rand(1,10),
                'pgrado' => $grados[rand(0,2)],
            ]); $unUsuario->assignRole('estudiante');
        }
    }
}
