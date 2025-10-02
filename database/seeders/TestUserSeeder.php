<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create a test company
        $company = Company::firstOrCreate(
            ['name' => 'Test Company Ltd.'],
            [
                'address' => '123 Test Street, Hanoi, Vietnam',
                'phone' => '+84 123 456 789',
                'email' => 'info@testcompany.com',
                'website' => 'https://www.testcompany.com',
                'size' => 'medium',
                'employee_count' => 150,
                'industry' => 'Technology',
                'description' => 'A test company for safety incident management system demonstration.',
            ]
        );

        // Create a test manager user if not exists
        $manager = User::firstOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Test Manager',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'company_id' => $company->id,
            ]
        );

        // Update company_id if it was null
        if (!$manager->company_id) {
            $manager->company_id = $company->id;
            $manager->save();
        }

        // Create a test employee user if not exists
        $employee = User::firstOrCreate(
            ['email' => 'employee@test.com'],
            [
                'name' => 'Test Employee',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'company_id' => $company->id,
            ]
        );

        // Update company_id if it was null
        if (!$employee->company_id) {
            $employee->company_id = $company->id;
            $employee->save();
        }

        $this->command->info('Test company and users created successfully!');
        $this->command->info('Company: ' . $company->name);
        $this->command->info('Manager: manager@test.com / password');
        $this->command->info('Employee: employee@test.com / password');
    }
}