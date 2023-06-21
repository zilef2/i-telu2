<?php

namespace Database\Seeders;

use App\Models\User;
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
            'jose',
            'madrid',
            'felizzola',
            'marta',
            'liliana',
            'mabel',
            'miguel',
        ];
        foreach ($nombresGenericos as $value) {
            $unUsuario = User::create([
                'name'              => $value,
                'email'             => $value.'@'.$value.'.com',
                'password'          => bcrypt('asd+-*/'),
                'email_verified_at' => date('Y-m-d H:i'),
                'posicion_id' => 1
            ]); $unUsuario->assignRole('estudiante');
        }
    }
}
