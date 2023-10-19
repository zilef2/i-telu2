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

        $superadmin = Role::create(['name' => 'superadmin']);
        $admin = Role::create(['name' => 'admin']);


        $estudiante = Role::create(['name' => 'estudiante']);
        $profesor = Role::create(['name' => 'profesor']);
        $coordinador_de_programa = Role::create(['name' => 'coordinador_de_programa']);
        $coordinador_academico = Role::create(['name' => 'coordinador_academico']);


        $Models = [
            'user',
            'role',
            'permission',
            'universidad',
            'carrera',
            'materia',
            'Unidad',
            'subtopico',
            'ejercicio',
            'LosPromp',
            'Articulo',
            'Plan',
        ];
        $crudCompleto = ['delete', 'update', 'read', 'create'];


        foreach ($Models as $model) {
            foreach ($crudCompleto as $crud) {
                $superadmin->givePermissionTo([$crud . ' ' . $model]);
                $admin->givePermissionTo([$crud . ' ' . $model]);
            }
        }
        $superadmin->givePermissionTo(['isSuper', 'isAdmin', 'isCoorAcademico', 'isCoorPrograma', 'isProfesor', 'isEstudiante']);
        $admin->givePermissionTo(['isAdmin', 'isCoorAcademico', 'isCoorPrograma', 'isProfesor', 'isEstudiante']);

        $coordinador_academico->givePermissionTo(['isCoorAcademico']);
        $coordinador_de_programa->givePermissionTo(['isCoorPrograma']);
        $profesor->givePermissionTo(['isProfesor']);
        $estudiante->givePermissionTo(['isEstudiante']);

        $coordinador_academico->givePermissionTo([
            'read user', 'update user', 'create user',
            'read universidad', 'update universidad', 'inscribirUs universidad',
            'read carrera', 'update carrera', 'create carrera', 'inscribirUs carrera',
            'read materia', 'update materia', 'create materia', 'inscribirUs materia',
            'read Articulo', 'update Articulo', 'create Articulo','delete Articulo',
            'read Unidad', 'update Unidad', 'create Unidad',
            'read subtopico', 'update subtopico', 'create subtopico',
            'read ejercicio', 'update ejercicio', 'create ejercicio',
            'read Plan', 'update Plan', 'create Plan',
        ]);

        $coordinador_de_programa->givePermissionTo([
            'read carrera', 'cambiarNombre carrera', 'create carrera', 'inscribirUs universidad',
            'read materia', 'update materia', 'create materia', 'inscribirUs carrera',
            'read Unidad', 'update Unidad', 'create Unidad', 'inscribirUs materia',
            'read subtopico', 'update subtopico', 'create subtopico',
            'read ejercicio', 'update ejercicio', 'create ejercicio', 'delete ejercicio',
            'read Articulo', 'update Articulo', 'create Articulo',
            'read Plan', 'update Plan', 'create Plan',
        ]);

        $profesor->givePermissionTo([
            'read carrera',
            'read materia', 'update materia', 'create materia',
            'read Unidad', 'cambiarNombre Unidad', 'create Unidad',
            'read subtopico', 'update subtopico', 'create subtopico',
            'read ejercicio', 'update ejercicio', 'create ejercicio',
            'read Articulo', 'update Articulo', 'create Articulo',
            'read Plan', 'update Plan', 'create Plan',
        ]);

        $estudiante->givePermissionTo([
            'read Articulo', 'update Articulo', 'create Articulo',
            'read materia',
            'read Plan', 'update Plan', 'create Plan',
        ]);

        // $role->revokePermissionTo($permission); // $permission->removeRole($role);
    
    
    } // php artisan db:seed --class=RoleSeeder
}
