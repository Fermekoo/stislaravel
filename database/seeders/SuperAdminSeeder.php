<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $super_admin = User::create([
            'username'  => 'superadmin',
            'password'  => Hash::make('admin123'),
            'user_type' => 'admin'
        ]);

        $root_role = Role::create([
            'name'          => 'ROOT',
            'guard_name'    => 'web'
        ]);

        $super_admin->assignRole('ROOT');
    }
}
