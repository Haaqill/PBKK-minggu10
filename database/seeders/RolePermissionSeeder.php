<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definisikan Hak Akses (Permissions)
        $permissions = [
            'view-dashboard',
            'view-projects',
            'create-projects',
            'edit-projects',
            'delete-projects', // Hak Eksklusif Admin
            'manage-users',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 2. Buat Peran Utama: 'Admin' (Akses Penuh) dan 'Staf' (Akses Terbatas)
        $adminRole = Role::findOrCreate('Admin', 'web');
        $stafRole = Role::findOrCreate('Staf', 'web');

        // Admin mendapatkan SEMUA permissions (Akses Penuh)
        $adminRole->syncPermissions(Permission::all());

        // Staf hanya mendapatkan permissions terbatas (TANPA delete-projects dan manage-users)
        $stafRole->syncPermissions([
            'view-dashboard',
            'view-projects',
            'create-projects',
            'edit-projects',
        ]);

        // Peran kompatibilitas untuk evaluasi kelas (dosen & asdos)
        $dosenRole = Role::findOrCreate('dosen', 'web');
        $dosenRole->syncPermissions(Permission::all());

        $asdosRole = Role::findOrCreate('asdos', 'web');
        $asdosRole->syncPermissions([
            'view-dashboard',
            'view-projects',
            'create-projects',
            'edit-projects',
        ]);

        // 3. Buat Akun Uji Coba Demo
        // Akun Admin (Akses Penuh)
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator Utama',
                'role' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $adminUser->syncRoles(['Admin']);

        // Akun Staf (Akses Terbatas)
        $stafUser = User::updateOrCreate(
            ['email' => 'staf@staf.com'],
            [
                'name' => 'Staf Operasional',
                'role' => 'Staf',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $stafUser->syncRoles(['Staf']);

        // Akun Dosen
        $dosenUser = User::updateOrCreate(
            ['email' => 'dosen@gmail.com'],
            [
                'name' => 'Dr. Ir. Dosen Pembimbing, M.Kom.',
                'role' => 'dosen',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $dosenUser->syncRoles(['Admin', 'dosen']);

        // Akun Asdos
        $asdosUser = User::updateOrCreate(
            ['email' => 'asdos@gmail.com'],
            [
                'name' => 'Asisten Dosen PBKK',
                'role' => 'asdos',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $asdosUser->syncRoles(['Staf', 'asdos']);

        // Akun Mahasiswa
        $mhsUser = User::updateOrCreate(
            ['email' => 'hanif@gmail.com'],
            [
                'name' => 'Hanif',
                'role' => 'mahasiswa',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}

