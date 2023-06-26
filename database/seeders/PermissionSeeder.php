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
        Permission::create(['name' => 'isCoorAcademico']);
        Permission::create(['name' => 'isCoorPrograma']);
        Permission::create(['name' => 'isProfesor']);
        Permission::create(['name' => 'isEstudiante']);

        $crudCompleto = [ 'delete', 'update', 'read', 'create','cambiarNombre' ];
        $Models =[
            'user',
            'role',
            'permission',

            'universidad',
            'carrera',
            'materia',
            'tema',
            'subtopico',
            'ejercicio',
        ];
        foreach ($Models as $model) {
            foreach ($crudCompleto as $crud) {
                Permission::create(['name' => $crud.' '. $model]);
            }
        }

        $ModelsIns =[
            'universidad',
            'carrera',
            'materia',
        ];
        foreach ($ModelsIns as $model) {
            Permission::create(['name' => 'inscribirUs '. $model]);
        }

        // foreach ($Models as $model) {
        //     Permission::create(['name' => ' '. $model]);
        // }

    }
}
