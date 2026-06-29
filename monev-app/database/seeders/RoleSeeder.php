<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'reviewer']);
        Role::firstOrCreate(['name' => 'dosen']);

        $adminEmail    = env('SEED_ADMIN_EMAIL',    'admin@p3km.ac.id');
        $adminPassword = env('SEED_ADMIN_PASSWORD', 'password');
        $adminName     = env('SEED_ADMIN_NAME',     'Admin P3KM');

        $admin = User::firstOrCreate(
            ['email' => $adminEmail],
            ['name' => $adminName, 'password' => Hash::make($adminPassword)]
        );
        $admin->assignRole('admin');
    }
}
