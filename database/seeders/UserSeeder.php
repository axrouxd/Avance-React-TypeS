<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuarios
        User::create([
            'name' => 'Usuario Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => 1,
            'status' => 'active'
        ]);

        User::create([
            'name' => 'Usuario 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'role_id' => 2,
            'status' => 'active'
        ]);

        User::create([
            'name' => 'Usuario 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'role_id' => 2,
            'status' => 'active'
        ]);
        
    }
}
