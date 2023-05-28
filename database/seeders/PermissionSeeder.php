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

        
        // Permission::create(['name' => 'delete user']);
        // Permission::create(['name' => 'update user']);
        // Permission::create(['name' => 'read user']);
        // Permission::create(['name' => 'create user']);

        // Permission::create(['name' => 'delete role']);
        // Permission::create(['name' => 'update role']);
        // Permission::create(['name' => 'read role']);
        // Permission::create(['name' => 'create role']);

        // Permission::create(['name' => 'delete permission']);
        // Permission::create(['name' => 'update permission']);
        // Permission::create(['name' => 'read permission']);
        // Permission::create(['name' => 'create permission']);

        $crudCompleto = [ 'delete', 'update', 'read', 'create' ];
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
    }
}
