<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat permission
        Permission::create(['name' => 'create user']);
        Permission::create(['name' => 'export report']);
        Permission::create(['name' => 'assign region']);
        Permission::create(['name' => 'assign skp']);
        Permission::create(['name' => 'create activity']);
        Permission::create(['name' => 'create skp']);
        Permission::create(['name' => 'update profile']);
        Permission::create(['name' => 'approve']);
        Permission::create(['name' => 'manage password']);

        // Membuat role Admin dan memberikan semua permission
        $admin = Role::create(['name' => 'admin']);
        // $admin->givePermissionTo(Permission::all());

        // Membuat role Librarian dan memberikan permission khusus
        $employee = Role::create(['name' => 'pegawai']);
        // $employee->givePermissionTo(['create activity', 'update profile', 'create skp', 'manage password']);

        $superior = Role::create(['name' => 'inspektorat']);
        // $superior->givePermissionTo(['name' => 'update profile', 'approve']);

    }
}
