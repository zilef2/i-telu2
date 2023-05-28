<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
        $operator = Role::create([ 'name'=>'operator' ]);

        $crudCompleto = [ 'delete', 'update', 'read', 'create' ];
        // $RU = [ 'update', 'read' ];
        // $CR = [ 'create', 'read' ];

        $Models =[
            'user',
            'role',
            'permission',
            'universidad',
            'ejercicio',
        ];

        foreach ($Models as $model) {
            foreach ($crudCompleto as $crud) {
                $superadmin->givePermissionTo([ $crud.' '.$model ]);
                $admin->givePermissionTo([ $crud.' '.$model ]);
            }
        }

        $superadmin->givePermissionTo([ 'isSuper', 'isAdmin' ]);
        $admin->givePermissionTo([ 'isAdmin', ]);

        $operator->givePermissionTo([
             //ejercicio
            'read ejercicio',
            'create ejercicio',
            'delete ejercicio',
        ]);

        // $role->revokePermissionTo($permission);
        // $permission->removeRole($role);
    }
}
