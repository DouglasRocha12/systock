<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'status' => 'Active',
            'cpf' => '000.000.000-00',
            'password' => bcrypt('123456789')
        ]);

        Role::create(['name' => 'admin']);

        Permission::create(['name' => 'all']);

        $role = Role::findByName('admin');

        $permission = Permission::findByName('all');

        $role->givePermissionTo($permission);

        $user = User::find(1);
        $user->assignRole('admin');


        $this->call([
            CategorySeeder::class,
            ProductSeeder::class
        ]);
    }
}
