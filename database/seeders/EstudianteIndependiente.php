<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstudianteIndependiente extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Permission::create(['name' => 'matricularEnUniversidad']);
        Permission::create(['name' => 'matricularEnCarrera']);
        Permission::create(['name' => 'matricularEnMateria']);
        Permission::create(['name' => 'isIndependiente']);

        $estudiante_independiente = Role::firstOrCreate(['name' => 'estudiante_independiente']);
        $estudiante_independiente->givePermissionTo([
            'read materia', 'update materia', 'create materia',
            'read Unidad', 'cambiarNombre Unidad', 'create Unidad',
            'read subtopico', 'update subtopico', 'create subtopico',
            'read ejercicio', 'update ejercicio', 'create ejercicio',
            'read Articulo', 'update Articulo', 'create Articulo',
            'read Plan', 'update Plan', 'create Plan',
            'isIndependiente'
        ]);

        //los estudiantes no pueden matricular nada.

        $profesor = Role::Where('name','profesor')->first();
        $profesor->givePermissionTo([
            'matricularEnUniversidad',
            'matricularEnCarrera',
            'matricularEnMateria'
        ]);

        $superadmin = Role::Where('name','superadmin')->first();
        $superadmin->givePermissionTo([
            'matricularEnUniversidad',
            'matricularEnCarrera',
            'matricularEnMateria','isIndependiente'
        ]);
        $admin = Role::Where('name','admin')->first();
        $admin->givePermissionTo([
            'matricularEnUniversidad',
            'matricularEnCarrera',
            'matricularEnMateria','isIndependiente'
        ]);
        $coordinador_academico = Role::Where('name','coordinador_academico')->first();
        $coordinador_academico->givePermissionTo([
            'matricularEnUniversidad',
            'matricularEnCarrera',
            'matricularEnMateria'
        ]);
        $coordinador_de_programa = Role::Where('name','coordinador_de_programa')->first();
        $coordinador_de_programa->givePermissionTo([
            'matricularEnUniversidad',
            'matricularEnCarrera',
            'matricularEnMateria'
        ]);
    }
}
//php artisan db:seed --class=EstudianteIndependiente
