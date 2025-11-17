<?php

namespace Database\Seeders;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Creamos los roles
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleEditor = Role::create(['name' => 'editor']);
        $roleViewer = Role::create(['name' => 'viewer']);

        //Creamos los permisos
        Permission::create(['name' => 'create task']);
        Permission::create(['name' => 'edit task']);
        Permission::create(['name' => 'delete task']);
        Permission::create(['name' => 'view task']);

        //Asignamos permisos
        $roleAdmin->givePermissionTo(Permission::all());
        $roleEditor->givePermissionTo(['create task', 'edit task', 'view task', 'delete task']);
        $roleViewer->givePermissionTo(['view task']);

        //Creo el usuario admin
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@mail.com',
            'password' => Hash::make('admin')
        ]);

        //Le asigno el rol
        $admin->assignRole('admin');

        //Creo el usuario editor
        $editor = User::factory()->create([
            'name' => 'Editor',
            'email' => 'editor@mail.com',
            'password' => Hash::make('editor')
        ]);

        //Le asigno el rol
        $editor->assignRole('editor');

                //Creo el usuario editor
        $viewer = User::factory()->create([
            'name' => 'Viewer',
            'email' => 'viewer@mail.com',
            'password' => Hash::make('viewer')
        ]);

        //Le asigno el rol
        $viewer->assignRole('viewer');
    }
}
