<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        
        $superadmin = Role::create([ 'name'=>'superadmin' ]);
        $admin = Role::create([ 'name'=>'admin' ]);


        $estudiante = Role::create([ 'name'=>'estudiante' ]);
        $profesor = Role::create([ 'name'=>'profesor' ]);
        $coordinador_de_programa = Role::create([ 'name'=>'coordinador_de_programa' ]);
        $coordinador_academico = Role::create([ 'name'=>'coordinador_academico' ]);


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
        $crudCompleto = [ 'delete', 'update', 'read', 'create' ];


        foreach ($Models as $model) {
            foreach ($crudCompleto as $crud) {
                $superadmin->givePermissionTo([ $crud.' '.$model ]);
                $admin->givePermissionTo([ $crud.' '.$model ]);
            }
        }
        $superadmin->givePermissionTo([ 'isSuper', 'isAdmin','isCoorAcademico', 'isCoorPrograma' ]);
        $admin->givePermissionTo(['isAdmin','isCoorAcademico', 'isCoorPrograma' ]);
        $coordinador_academico->givePermissionTo(['isCoorAcademico']);
        $coordinador_de_programa->givePermissionTo(['isCoorPrograma']);
        $profesor->givePermissionTo(['isProfesor']);
        $estudiante->givePermissionTo(['isEstudiante']);

        $coordinador_academico->givePermissionTo([
            'read user', 'update user', 'create user', 'delete user',
            'read universidad', 'update universidad', 'create universidad', 'delete universidad',
            'read carrera', 'update carrera', 'create carrera', 'delete carrera',
            'read materia', 'update materia', 'create materia', 'delete materia',
            'read tema', 'update tema', 'create tema', 'delete tema',
            'read subtopico', 'update subtopico', 'create subtopico', 'delete subtopico',
            'read ejercicio', 'update ejercicio', 'create ejercicio', 'delete ejercicio',
        ]);
        $coordinador_de_programa->givePermissionTo([
            'read universidad', 'update universidad', 'create universidad', 'delete universidad',
            'read carrera', 'update carrera', 'create carrera', 'delete carrera',
            'read materia', 'update materia', 'create materia', 'delete materia',
            'read tema', 'update tema', 'create tema', 'delete tema',
            'read subtopico', 'update subtopico', 'create subtopico', 'delete subtopico',
            'read ejercicio', 'update ejercicio', 'create ejercicio', 'delete ejercicio',
        ]);

        $profesor->givePermissionTo([
            'read universidad',
            'read carrera',
            'read materia', 'update materia', 'create materia', 'delete materia',
            'read tema', 'update tema', 'create tema', 'delete tema',
            'read subtopico', 'update subtopico', 'create subtopico', 'delete subtopico',
            'read ejercicio', 'update ejercicio', 'create ejercicio', 'delete ejercicio',
        ]);
        $estudiante->givePermissionTo([
            // 'read universidad',
            'read carrera',
            'read materia',
            'read tema',
            'read subtopico',
            'read ejercicio', // 'update ejercicio', // 'create ejercicio', // 'delete ejercicio',
        ]);

        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
