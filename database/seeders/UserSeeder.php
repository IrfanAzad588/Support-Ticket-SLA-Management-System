<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Support Agent 1',
            'email' => 'agent1@example.com',
            'password' => Hash::make('agent1password123'),
            'role' => 'agent',
        ]);

        User::create([
            'name' => 'Support Agent 2',
            'email' => 'agent2@example.com',
            'password' => Hash::make('agent2password123'),
            'role' => 'agent',
        ]);

        User::create([
            'name' => 'Customer User 1',
            'email' => 'customer1@example.com',
            'password' => Hash::make('customer1password123'),
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Customer User 2',
            'email' => 'customer2@example.com',
            'password' => Hash::make('customer2password123'),
            'role' => 'customer',
        ]);
    }
}
