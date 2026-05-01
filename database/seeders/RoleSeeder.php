<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'Super Admin']);
        $admin = Role::create(['name' => 'Admin']);
        $badanpublik = Role::create(['name' => 'Badan Publik']);

        $admin->givePermissionTo([
            'view-beranda',
            'edit-profile',
            'verify-saq',
            'view-verifikator',
            'create-verifikator',
            'edit-verifikator',
            'delete-verifikator'
        ]);

        $badanpublik->givePermissionTo([
            'view-beranda',
            'edit-profile',
            'view-profile',
            'edit-profile',
            'view-kuesioner',
            'edit-kuesioner',
        ]);
    }
}
