<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use DB;
use App\Models\User;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $default_user_value = [
            'email_verified_at' => now(),
            'password' => bcrypt('adminadmin')
        ];

        // DB::beginTransaction();
        // try {
            $staff = User::create(array_merge([
                'email' => 'staff@gmail.com',
                'name' => 'staff',
            ], $default_user_value));
    
            $admin = User::create(array_merge([
                'email' => 'admin@gmail.com',
                'name' => 'admin',
            ], $default_user_value));
    
            $role_staff = Role::create(['name' => 'staff']);
            $role_admin = Role::create(['name' => 'admin']);
    
            $permission = Permission::create(['name' => 'read role']);
            $permission = Permission::create(['name' => 'create role']);
            $permission = Permission::create(['name' => 'update role']);
            $permission = Permission::create(['name' => 'delete role']);

            $role_admin->givePermissionTo('read role');
            $role_admin->givePermissionTo('create role');
            $role_admin->givePermissionTo('update role');
            $role_admin->givePermissionTo('delete role');
    
            $staff->assignRole('staff');
            $admin->assignRole('admin');

        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        // }
        
        

    }
}
