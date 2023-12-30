<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ONETIMERoleSeeder_plans extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        $superadmin = Role::findByName('superadmin');
        $admin = Role::findByName('admin');
        $coordinador_academico = Role::findByName('coordinador_academico');
        $coordinador_de_programa = Role::findByName('coordinador_de_programa');
        $profesor = Role::findByName('profesor');
        $estudiante = Role::findByName('estudiante');


        $crudCompleto = ['delete', 'update', 'read', 'create', 'cambiarNombre'];

        foreach ($crudCompleto as $crud) {
            Permission::create(['name' => $crud . ' ' . 'Plan']);
        }


        $Models = [
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
            'read Plan', 'update Plan', 'create Plan',
        ]);

        $coordinador_de_programa->givePermissionTo([
            'read Plan', 'update Plan', 'create Plan',
        ]);

        $profesor->givePermissionTo([
            'read Plan', 'update Plan', 'create Plan',
        ]);

        $estudiante->givePermissionTo([
            'read Plan', 'update Plan', 'create Plan',
        ]);

    } // php artisan db:seed --class=ONETIMERoleSeeder_plans
}
