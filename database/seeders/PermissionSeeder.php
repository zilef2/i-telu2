<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::create(['name' => 'isSuper']);
        Permission::create(['name' => 'isAdmin']);




        //otros cargos NO_ADMIN
        $nombresDeCargos = [
            'trabajador',
        ];
        $Models = [
            'user',
            'role',
            'permission',
            'parametro',

        ];



        foreach ($nombresDeCargos as $key => $value) {
            Permission::create(['name' => 'is'.$value]);
        }

        $crudCompleto = ['delete', 'update', 'read', 'create', 'cambiarNombre'];
        foreach ($Models as $model) {
            foreach ($crudCompleto as $crud) {
                Permission::create(['name' => $crud . ' ' . $model]);
            }
        }

        
        //# Inscripciones (muchos a muchos)
        // $ModelsIns = [
        //     'universidad',
        //     'carrera',
        //     'materia',
        // ];
        // foreach ($ModelsIns as $model) {
        //     Permission::create(['name' => 'inscribirUs ' . $model]);
        // }
    }
}
