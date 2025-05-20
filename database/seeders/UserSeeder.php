<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create Admin User (using firstOrCreate to avoid duplicates)
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Create Example Employee User
        $employee = User::firstOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Example Employee',
                'password' => Hash::make('password'),
                'role' => 'employee',
                'email_verified_at' => now(),
            ]
        );

        // Create Employee record for the example employee if it doesn't exist
        if (!$employee->employee) {
            Employee::create([
                'user_id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'position' => 'Software Developer',
                'department' => 'Engineering',
                'phone' => '123-456-7890',
                'base_salary' => 5000000,
            ]);
        }
    }
}
