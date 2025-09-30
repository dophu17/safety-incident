<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create a test manager user if not exists
        User::firstOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Test Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
            ]
        );

        // Create a test employee user if not exists
        User::firstOrCreate(
            ['email' => 'employee@test.com'],
            [
                'name' => 'Test Employee',
                'password' => Hash::make('password'),
                'role' => 'employee',
            ]
        );

        $this->command->info('Test users created successfully!');
        $this->command->info('Manager: manager@test.com / password');
        $this->command->info('Employee: employee@test.com / password');
    }
}