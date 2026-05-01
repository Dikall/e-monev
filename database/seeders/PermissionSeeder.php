<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view-dashboard',
            'view-beranda',
            'view-profile',
            'edit-profile',
            'reset-password',
            'nonaktifkan-akun',
            'view-kuesioner',
            'create-kuesioner',
            'edit-kuesioner',
            'delete-kuesioner',
            'verify-saq',
            'create-role',
            'edit-role',
            'delete-role',
            'create-user',
            'edit-user',
            'delete-user',
            'view-verifikator',
            'create-verifikator',
            'edit-verifikator',
            'delete-verifikator',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
