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
            $admin = User::create(array_merge([
                'email' => 'no-reply@jgu.ac.id',
                'name' => 'Admin',
                'username' => 'admin',
            ], $default_user_value));

            $staff = User::create(array_merge([
                'email' => 'afikri124@gmail.com',
                'name' => 'Staff',
                'username' => 'staff',
            ], $default_user_value));
    
    
            $role_admin = Role::create(['name' => 'admin', 'color' => '#000000', 'description' => 'Administrator']);
            $role_staff = Role::create(['name' => 'staff', 'color' => '#ff0000', 'description' => 'Staff only']);
    
            $staff->assignRole('staff');
            $admin->assignRole('admin');

        //     DB::commit();
        // } catch (\Throwable $th) {
        //     DB::rollBack();
        // }
        
        

    }
}
